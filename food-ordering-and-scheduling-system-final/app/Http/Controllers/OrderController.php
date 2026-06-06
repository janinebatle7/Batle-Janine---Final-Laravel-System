<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())->latest()->get();
        return view('customer.orders', compact('orders'));
    }

    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) return back()->with('error', 'Cart is empty');

        $request->validate([
            'payment_method' => 'required|in:Cash,GCash',
            'order_type' => 'required|in:Immediate,Scheduled',
            'scheduled_datetime' => 'nullable|required_if:order_type,Scheduled|date',
            'gcash_ref' => 'nullable|required_if:payment_method,GCash',
            'proof_image' => 'nullable|image|max:5120' // 5MB max
        ]);

        $openHours = \App\Http\Controllers\AdminDashboardController::getOpenHoursSettings();

        if ($openHours['override_closed']) {
            return back()->with('error', 'Cannot place order: ' . $openHours['override_closed_message'])->withInput();
        }

        if (!$openHours['always_open']) {
            if ($request->order_type === 'Scheduled') {
                $checkTime = Carbon::parse($request->scheduled_datetime)->setTimezone('Asia/Manila');
            } else {
                $checkTime = Carbon::now('Asia/Manila');
            }

            $dayName = $checkTime->format('l');
            $dayConfig = $openHours['days'][$dayName] ?? null;

            if (!$dayConfig || !($dayConfig['is_open'] ?? true)) {
                $errMsg = $request->order_type === 'Scheduled'
                    ? "We are closed on {$dayName}s. Please pick a different schedule."
                    : "We are closed today ({$dayName}). Please select Pre-Order to book for another day!";
                return back()->with('error', $errMsg)->withInput();
            }

            $targetTime = $checkTime->format('H:i');
            $openTime = $dayConfig['open_time'] ?? '09:00';
            $closeTime = $dayConfig['close_time'] ?? '21:00';

            if ($targetTime < $openTime || $targetTime > $closeTime) {
                try {
                    $openFormatted = Carbon::createFromFormat('H:i', $openTime)->format('g:i A');
                    $closeFormatted = Carbon::createFromFormat('H:i', $closeTime)->format('g:i A');
                } catch (\Exception $ex) {
                    $openFormatted = '9:00 AM';
                    $closeFormatted = '9:00 PM';
                }

                $errMsg = $request->order_type === 'Scheduled'
                    ? "Preferred schedule ({$checkTime->format('g:i A')}) is outside our operating hours on {$dayName}s ({$openFormatted} - {$closeFormatted})."
                    : "We are currently closed. Our hours for today ({$dayName}) are {$openFormatted} - {$closeFormatted}. Please Pre-Order instead!";
                return back()->with('error', $errMsg)->withInput();
            }
        }

        return DB::transaction(function () use ($request, $cart) {
            $order = Order::create([
                'user_id' => auth()->id(),
                'total_amount' => $request->total_amount,
                'order_type' => $request->order_type,
                'scheduled_datetime' => $request->scheduled_datetime,
                'status' => 'Pending',
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'GCash' ? 'Pending Verification' : 'Unpaid'
            ]);

            foreach ($cart as $id => $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $id,
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                    'special_instructions' => $item['special_instructions'] ?? null
                ]);
            }

            if ($request->payment_method === 'GCash') {
                $proofPath = null;
                if ($request->hasFile('proof_image')) {
                    $file = $request->file('proof_image');
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/proofs'), $filename);
                    $proofPath = '/uploads/proofs/' . $filename;
                }

                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => 'GCash',
                    'amount' => $request->total_amount,
                    'payment_status' => 'Pending Verification',
                    'reference_number' => $request->gcash_ref,
                    'proof_image' => $proofPath
                ]);
            }

            session()->forget('cart');
            return redirect()->route('orders.index')->with('success', 'Order placed successfully! We will process it shortly.');
        });
    }

    /**
     * Cancel own order
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!in_array($order->status, ['Pending', 'Confirmed'])) {
            return redirect()->route('orders.index')->with('error', "Cannot cancel order #ORD-{$order->id}: It is already in kitchen preparation or completed.");
        }

        $order->status = 'Cancelled';
        $order->save();

        if ($order->payment) {
            $payment = $order->payment;
            $payment->payment_status = 'Failed';
            $payment->save();

            $order->payment_status = 'Failed';
            $order->save();
        }

        return redirect()->route('orders.index')->with('success', "Order #ORD-{$order->id} cancelled successfully.");
    }

    /**
     * Delete own order history
     */
    public function destroy(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if (in_array($order->status, ['Preparing', 'Ready'])) {
            return redirect()->route('orders.index')->with('error', "Cannot delete order #ORD-{$order->id} while it is currently being prepared in the kitchen.");
        }

        $order->delete();

        return redirect()->route('orders.index')->with('success', "Order #ORD-{$order->id} deleted from your history.");
    }
}
