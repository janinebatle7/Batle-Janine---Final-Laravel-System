<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminDashboardController extends Controller
{
    /**
     * Main Overview
     */
    public function index()
    {
        // Staff members get a more restricted view focused on active order volume
        $isStaff = auth()->user()->role === 'staff';

        $stats = [
            'total_sales' => $isStaff ? 0 : Order::where('payment_status', 'Paid')->sum('total_amount'),
            'pending_orders' => Order::where('status', 'Pending')->count(),
            'kitchen_queue' => Order::whereIn('status', ['Confirmed', 'Preparing', 'Ready'])->count(),
            'needs_verification' => Order::where('payment_status', 'Pending Verification')->count(),
        ];

        $recent_orders = Order::with(['user', 'payment'])->latest()->take(10)->get();

        // Admin-only sales reporting
        $category_sales = $isStaff ? collect() : DB::table('order_details')
            ->join('menu_items', 'order_details.menu_item_id', '=', 'menu_items.id')
            ->select('menu_items.category', DB::raw('SUM(order_details.subtotal) as total'))
            ->groupBy('menu_items.category')
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_orders', 'category_sales'));
    }

    public function menuIndex()
    {
        $items = MenuItem::all();
        $categories = \App\Models\Category::all();
        return view('admin.menu_index', compact('items', 'categories'));
    }

    public function menuCreate()
    {
        $categories = Category::all();
        return view('admin.menu_create', compact('categories'));
    }

    public function menuStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|exists:categories,name',
            'image_url' => 'nullable|url|max:2048',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $imagePath = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=800'; // beautiful food fallback

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            // Store in public/uploads/menu
            $file->move(public_path('uploads/menu'), $filename);
            $imagePath = '/uploads/menu/' . $filename;
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->image_url;
        }

        MenuItem::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category' => $request->category,
            'image' => $imagePath,
            'availability_status' => true,
        ]);

        return redirect()->route('admin.menu.index')->with('success', 'Menu item added successfully!');
    }

    /**
     * Kitchen Live Queue
     */
    public function kitchen()
    {
        $orders = Order::with(['details.menuItem', 'user'])
            ->whereIn('status', ['Pending', 'Confirmed', 'Preparing', 'Ready'])
            ->orderByRaw('CASE WHEN scheduled_datetime IS NOT NULL THEN scheduled_datetime ELSE created_at END ASC')
            ->get();

        return view('admin.kitchen', compact('orders'));
    }

    /**
     * Unified Order & Activity Tracking Log
     */
    public function ordersLog()
    {
        $orders = Order::with(['details.menuItem', 'user', 'payment'])
            ->latest()
            ->get();

        return view('admin.orders_log', compact('orders'));
    }

    /**
     * Status Management
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:Pending,Confirmed,Preparing,Ready,Completed,Cancelled']);

        $order->update(['status' => $request->status]);

        // Auto-mark as paid if completed cash order
        if ($request->status === 'Completed' && $order->payment_method === 'Cash') {
            $order->update(['payment_status' => 'Paid']);
        }

        return back()->with('success', "Order status updated to {$request->status}");
    }

    /**
     * Inventory Control
     */
    public function toggleAvailability(MenuItem $item)
    {
        $item->update(['availability_status' => !$item->availability_status]);
        return back()->with('success', 'Item availability updated.');
    }

    /**
     * Verify GCash Payment
     */
    public function verifyPayment(Order $order)
    {
        $order->update(['payment_status' => 'Paid']);

        $payment = Payment::where('order_id', $order->id)->first();
        if ($payment) {
            $payment->update([
                'payment_status' => 'Paid',
                'verified_by' => auth()->id()
            ]);
        }

        return back()->with('success', 'Payment verified successfully!');
    }

    /**
     * Categories Listing + Unified Add Form
     */
    public function categoriesIndex()
    {
        $categories = Category::withCount('menuItems')->get();
        return view('admin.categories_index', compact('categories'));
    }

    /**
     * Store new category
     */
    public function categoriesStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
    }

    /**
     * Edit Category Page
     */
    public function categoriesEdit(Category $category)
    {
        return view('admin.categories_edit', compact('category'));
    }

    /**
     * Update Category name
     */
    public function categoriesUpdate(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $oldName = $category->name;
        $category->update([
            'name' => $request->name,
        ]);

        MenuItem::where('category', $oldName)->update([
            'category' => $request->name,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    /**
     * Destroy Category
     */
    public function categoriesDestroy(Category $category)
    {
        $oldName = $category->name;

        // Ensure fallback category exists
        $fallback = Category::where('id', '!=', $category->id)->first();
        $fallbackName = $fallback ? $fallback->name : 'Meals';

        MenuItem::where('category', $oldName)->update([
            'category' => $fallbackName,
        ]);

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully! Associated menu items reassigned to ' . $fallbackName . '.');
    }

    /**
     * Edit Menu Item
     */
    public function menuEdit(MenuItem $item)
    {
        $categories = Category::all();
        return view('admin.menu_edit', compact('item', 'categories'));
    }

    /**
     * Update Menu Item
     */
    public function menuUpdate(Request $request, MenuItem $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|exists:categories,name',
            'image_url' => 'nullable|url|max:2048',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $imagePath = $item->image;

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/menu'), $filename);
            $imagePath = '/uploads/menu/' . $filename;
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->image_url;
        }

        $item->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category' => $request->category,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.menu.index')->with('success', 'Menu item updated successfully!');
    }

    /**
     * Delete Menu Item
     */
    public function menuDestroy(MenuItem $item)
    {
        try {
            // Drop the old non-nullable foreign key and recreate it with ON DELETE SET NULL on the fly
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE order_details DROP FOREIGN KEY order_details_menu_item_id_foreign");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE order_details MODIFY menu_item_id BIGINT UNSIGNED NULL");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE order_details ADD CONSTRAINT order_details_menu_item_id_foreign FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE SET NULL");
        } catch (\Exception $e) {
            // Already altered or other DB environments (e.g. SQLite if testing)
        }

        $item->delete();
        return redirect()->route('admin.menu.index')->with('success', 'Menu item deleted successfully!');
    }

    /**
     * Show Admin Settings (Manage Account & GCash Payment details)
     */
    public function settingsShow()
    {
        $user = auth()->user();
        $gcash = self::getGcashSettings();
        $openHours = self::getOpenHoursSettings();
        return view('admin.settings', compact('user', 'gcash', 'openHours'));
    }

    /**
     * Update Admin Profile (name and email, and profile image)
     */
    public function settingsUpdateProfile(Request $request)
    {
        $user = auth()->user();

        if (isset($_FILES['profile_image_file']) && $_FILES['profile_image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $errCode = $_FILES['profile_image_file']['error'];
            if ($errCode === UPLOAD_ERR_INI_SIZE || $errCode === UPLOAD_ERR_FORM_SIZE) {
                $maxSize = ini_get('upload_max_filesize') ?: '15M';
                return back()->withErrors([
                    'profile_image_file' => "The uploaded profile image file is too large. Please select an image smaller than {$maxSize}."
                ])->withInput();
            } elseif ($errCode !== UPLOAD_ERR_OK) {
                return back()->withErrors([
                    'profile_image_file' => "The profile image failed to upload. Please try a different or smaller image."
                ])->withInput();
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'profile_image_url' => 'nullable|url|max:2048',
            'profile_image_file' => 'bail|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:15360',
        ], [
            'profile_image_file.max' => 'The uploaded profile image file is too large. Please select an image smaller than 15 Megabytes (15MB).',
            'profile_image_file.image' => 'The selected file must be a valid image format (JPEG, PNG, JPG, GIF, WebP).',
            'profile_image_file.mimes' => 'The selected file must be a valid image format (JPEG, PNG, JPG, GIF, WebP).',
        ]);

        $imagePath = $user->profile_image;

        if ($request->hasFile('profile_image_file')) {
            $file = $request->file('profile_image_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/profile'), $filename);
            $imagePath = '/uploads/profile/' . $filename;
        } elseif ($request->filled('profile_image_url')) {
            $imagePath = $request->profile_image_url;
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'profile_image' => $imagePath,
        ]);

        return redirect()->route('admin.settings')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update Admin Password
     */
    public function settingsUpdatePassword(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.settings')->with('success', 'Password changed successfully!');
    }

    /**
     * Update GCash Configuration Details
     */
    public function settingsUpdateGcash(Request $request)
    {
        $request->validate([
            'number' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'qr_code_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'qr_code_url' => 'nullable|url|max:2048',
        ]);

        $current = self::getGcashSettings();
        $qrCode = $current['qr_code'];

        if ($request->hasFile('qr_code_file')) {
            $file = $request->file('qr_code_file');
            $filename = 'gcash_qr_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/gcash'), $filename);
            $qrCode = '/uploads/gcash/' . $filename;
        } elseif ($request->filled('qr_code_url')) {
            $qrCode = $request->qr_code_url;
        }

        $data = [
            'number' => $request->number,
            'name' => $request->name,
            'qr_code' => $qrCode
        ];

        file_put_contents(storage_path('app/gcash_settings.json'), json_encode($data, JSON_PRETTY_PRINT));

        return redirect()->route('admin.settings')->with('success', 'GCash settings and dynamic QR code updated!');
    }

    /**
     * Helper to retrieve GCash settings
     */
    public static function getGcashSettings()
    {
        $path = storage_path('app/gcash_settings.json');
        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
            return array_merge([
                'number' => '0912 345 6789',
                'name' => 'Clara B. | GCash Professional',
                'qr_code' => null
            ], $data ?: []);
        }
        return [
            'number' => '0912 345 6789',
            'name' => 'Clara B. | GCash Professional',
            'qr_code' => null
        ];
    }

    /**
     * Update Open Hours Configurations
     */
    public function settingsUpdateOpenHours(Request $request)
    {
        $request->validate([
            'always_open' => 'nullable',
            'override_closed' => 'nullable',
            'override_closed_message' => 'required|string|max:500',
            'days' => 'required|array',
        ]);

        $daysData = [];
        $allowedDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        foreach ($allowedDays as $day) {
            $is_open = isset($request->days[$day]['is_open']) && ($request->days[$day]['is_open'] == '1' || $request->days[$day]['is_open'] == 'on');
            $open_time = $request->days[$day]['open_time'] ?? '09:00';
            $close_time = $request->days[$day]['close_time'] ?? '21:00';

            $daysData[$day] = [
                'is_open' => $is_open,
                'open_time' => $open_time,
                'close_time' => $close_time
            ];
        }

        $data = [
            'always_open' => $request->has('always_open'),
            'override_closed' => $request->has('override_closed'),
            'override_closed_message' => $request->override_closed_message,
            'days' => $daysData
        ];

        file_put_contents(storage_path('app/open_hours_settings.json'), json_encode($data, JSON_PRETTY_PRINT));

        return redirect()->route('admin.settings')->with('success', 'Open Hours settings updated successfully!');
    }

    /**
     * Helper to retrieve Open Hours settings
     */
    public static function getOpenHoursSettings()
    {
        $path = storage_path('app/open_hours_settings.json');

        $defaults = [
            'always_open' => false,
            'override_closed' => false,
            'override_closed_message' => 'Our ordering system is temporarily offline. Please come back later!',
            'days' => [
                'Monday'    => ['is_open' => true, 'open_time' => '09:00', 'close_time' => '21:00'],
                'Tuesday'   => ['is_open' => true, 'open_time' => '09:00', 'close_time' => '21:00'],
                'Wednesday' => ['is_open' => true, 'open_time' => '09:00', 'close_time' => '21:00'],
                'Thursday'  => ['is_open' => true, 'open_time' => '09:00', 'close_time' => '21:00'],
                'Friday'    => ['is_open' => true, 'open_time' => '09:00', 'close_time' => '21:00'],
                'Saturday'  => ['is_open' => true, 'open_time' => '09:00', 'close_time' => '21:00'],
                'Sunday'    => ['is_open' => true, 'open_time' => '09:00', 'close_time' => '21:00'],
            ]
        ];

        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
            if (is_array($data)) {
                if (isset($data['days']) && is_array($data['days'])) {
                    $data['days'] = array_merge($defaults['days'], $data['days']);
                }
                return array_merge($defaults, $data);
            }
        }

        return $defaults;
    }

    /**
     * User and Staff Management - Index
     */
    public function usersIndex(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');

        $query = \App\Models\User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role && in_array($role, ['admin', 'staff', 'customer'])) {
            $query->where('role', $role);
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users_index', compact('users', 'search', 'role'));
    }

    /**
     * User and Staff Management - Store Staff Only
     */
    public function usersStoreStaff(Request $request)
    {
        if (isset($_FILES['profile_image_file']) && $_FILES['profile_image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $errCode = $_FILES['profile_image_file']['error'];
            if ($errCode === UPLOAD_ERR_INI_SIZE || $errCode === UPLOAD_ERR_FORM_SIZE) {
                $maxSize = ini_get('upload_max_filesize') ?: '15M';
                return back()->withErrors([
                    'profile_image_file' => "The uploaded profile image file is too large. Please select an image smaller than {$maxSize}."
                ])->withInput();
            } elseif ($errCode !== UPLOAD_ERR_OK) {
                return back()->withErrors([
                    'profile_image_file' => "The profile image failed to upload. Please try a different or smaller image."
                ])->withInput();
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'profile_image_url' => 'nullable|url|max:2048',
            'profile_image_file' => 'bail|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:15360',
        ], [
            'profile_image_file.max' => 'The uploaded profile image file is too large. Please select an image smaller than 15 Megabytes (15MB).',
            'profile_image_file.image' => 'The selected file must be a valid image format (JPEG, PNG, JPG, GIF, WebP).',
            'profile_image_file.mimes' => 'The selected file must be a valid image format (JPEG, PNG, JPG, GIF, WebP).',
        ]);

        $imagePath = null;

        if ($request->hasFile('profile_image_file')) {
            $file = $request->file('profile_image_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/profile'), $filename);
            $imagePath = '/uploads/profile/' . $filename;
        } elseif ($request->filled('profile_image_url')) {
            $imagePath = $request->profile_image_url;
        }

        \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff', // Add staff only!
            'profile_image' => $imagePath,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Staff member added successfully!');
    }

    /**
     * User and Staff Management - Update (Edit name, email, password, role, and profile image)
     */
    public function usersUpdate(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);

        if (isset($_FILES['profile_image_file']) && $_FILES['profile_image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $errCode = $_FILES['profile_image_file']['error'];
            if ($errCode === UPLOAD_ERR_INI_SIZE || $errCode === UPLOAD_ERR_FORM_SIZE) {
                $maxSize = ini_get('upload_max_filesize') ?: '15M';
                return back()->withErrors([
                    'profile_image_file' => "The uploaded profile image file is too large. Please select an image smaller than {$maxSize}."
                ])->withInput();
            } elseif ($errCode !== UPLOAD_ERR_OK) {
                return back()->withErrors([
                    'profile_image_file' => "The profile image failed to upload. Please try a different or smaller image."
                ])->withInput();
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,staff,customer',
            'password' => 'nullable|string|min:6|confirmed',
            'profile_image_url' => 'nullable|url|max:2048',
            'profile_image_file' => 'bail|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:15360',
        ], [
            'profile_image_file.max' => 'The uploaded profile image file is too large. Please select an image smaller than 15 Megabytes (15MB).',
            'profile_image_file.image' => 'The selected file must be a valid image format (JPEG, PNG, JPG, GIF, WebP).',
            'profile_image_file.mimes' => 'The selected file must be a valid image format (JPEG, PNG, JPG, GIF, WebP).',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        $imagePath = $user->profile_image;

        if ($request->hasFile('profile_image_file')) {
            $file = $request->file('profile_image_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/profile'), $filename);
            $imagePath = '/uploads/profile/' . $filename;
        } elseif ($request->filled('profile_image_url')) {
            $imagePath = $request->profile_image_url;
        }

        $user->profile_image = $imagePath;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    /**
     * User and Staff Management - Delete
     */
    public function usersDestroy($id)
    {
        if (auth()->id() == $id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account!');
        }

        $user = \App\Models\User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }

    /**
     * Delete Order - Admin access only
     */
    public function ordersDestroy(Order $order)
    {
        // Delete related order details
        $order->details()->delete();

        // Delete related payment details if any
        if ($order->payment) {
            $order->payment->delete();
        }

        // Delete the main order
        $order->delete();

        return redirect()->route('admin.orders.log')->with('success', 'Order was successfully deleted from logs.');
    }
}
