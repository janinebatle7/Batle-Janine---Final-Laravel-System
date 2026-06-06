<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('customer.cart', compact('cart'));
    }

    public function add(MenuItem $item)
    {
        $openHours = \App\Http\Controllers\AdminDashboardController::getOpenHoursSettings();
        if ($openHours['override_closed'] ?? false) {
            $msg = $openHours['override_closed_message'] ?? 'The store is temporarily closed. Please come back later!';
            return redirect()->back()->with('error', 'Cannot add item: ' . $msg);
        }

        $cart = session()->get('cart', []);
        $qty = max(1, (int)request('quantity', 1));
        $instructions = (string)request('special_instructions', '');

        if(isset($cart[$item->id])) {
            $cart[$item->id]['quantity'] += $qty;
            if(!empty($instructions)) {
                if(!empty($cart[$item->id]['special_instructions'])) {
                    $cart[$item->id]['special_instructions'] .= '; ' . $instructions;
                } else {
                    $cart[$item->id]['special_instructions'] = $instructions;
                }
            }
        } else {
            $cart[$item->id] = [
                "id" => $item->id,
                "name" => $item->name,
                "quantity" => $qty,
                "price" => $item->price,
                "image" => $item->image,
                "special_instructions" => $instructions
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Item added to cart!');
    }

    public function update(Request $request)
    {
        if($request->id){
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                if ($request->has('quantity')) {
                    $cart[$request->id]["quantity"] = max(1, $request->quantity);
                }
                if ($request->has('special_instructions')) {
                    $cart[$request->id]["special_instructions"] = $request->special_instructions ?? "";
                }
                session()->put('cart', $cart);
            }
        }
        return redirect()->back()->with('success', 'Cart updated!');
    }

    public function remove(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
        }
        return redirect()->back()->with('success', 'Item removed from cart!');
    }
}
