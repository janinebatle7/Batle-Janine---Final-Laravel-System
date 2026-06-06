<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Dynamic Migration triggering to guarantee database column additions
try {
    if (!\Schema::hasColumn('users', 'profile_image')) {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    }
} catch (\Exception $e) {
    // Fail silently during deployment bootstraps
}

// Guest Routes
Route::get('/', [MenuController::class, 'index'])->name('menu');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Customer & General Auth Routes
Route::middleware(['auth'])->group(function () {
    // Menu & Search
    Route::get('/menu', [MenuController::class, 'index'])->name('menu');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{item}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

    // Checkout & Orders
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::get('/my-orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/my-orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/my-orders/{order}/delete', [OrderController::class, 'destroy'])->name('orders.delete');

    // General Settings (Profile & Password for staff and customers)
    Route::get('/settings', [AuthController::class, 'settingsShow'])->name('settings');
    Route::post('/settings/profile', [AuthController::class, 'settingsUpdateProfile'])->name('settings.profile');
    Route::post('/settings/password', [AuthController::class, 'settingsUpdatePassword'])->name('settings.password');
});

// Redirect legacy management routes to admin
Route::middleware(['auth'])->prefix('management')->group(function () {
    Route::get('/{any?}', function ($any = null) {
        return redirect()->to('/admin/' . $any);
    })->where('any', '.*');
});

// Shared Admin & Staff Routes
Route::middleware(['auth'])->prefix('admin')->group(function () {
    // Both Admin and Staff can access Kitchen and update statuses
    Route::middleware(['role:admin,staff'])->group(function () {
        Route::get('/kitchen', [AdminDashboardController::class, 'kitchen'])->name('admin.kitchen');
        Route::post('/orders/{order}/status', [AdminDashboardController::class, 'updateStatus'])->name('admin.orders.status');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard'); // Staff sees limited view
        Route::get('/orders-log', [AdminDashboardController::class, 'ordersLog'])->name('admin.orders.log');
        Route::post('/orders/{order}/verify', [AdminDashboardController::class, 'verifyPayment'])->name('admin.orders.verify');
    });

    // Only Admin can manage Menu/Inventory & Categories
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/menu', [AdminDashboardController::class, 'menuIndex'])->name('admin.menu.index');
        Route::get('/menu/create', [AdminDashboardController::class, 'menuCreate'])->name('admin.menu.create');
        Route::post('/menu/store', [AdminDashboardController::class, 'menuStore'])->name('admin.menu.store');
        Route::get('/menu/{item}/edit', [AdminDashboardController::class, 'menuEdit'])->name('admin.menu.edit');
        Route::post('/menu/{item}/update', [AdminDashboardController::class, 'menuUpdate'])->name('admin.menu.update');
        Route::post('/menu/{item}/delete', [AdminDashboardController::class, 'menuDestroy'])->name('admin.menu.destroy');
        Route::post('/menu/{item}/toggle', [AdminDashboardController::class, 'toggleAvailability'])->name('admin.menu.toggle');

        // Categories CRUD
        Route::get('/categories', [AdminDashboardController::class, 'categoriesIndex'])->name('admin.categories.index');
        Route::post('/categories', [AdminDashboardController::class, 'categoriesStore'])->name('admin.categories.store');
        Route::get('/categories/{category}/edit', [AdminDashboardController::class, 'categoriesEdit'])->name('admin.categories.edit');
        Route::post('/categories/{category}/update', [AdminDashboardController::class, 'categoriesUpdate'])->name('admin.categories.update');
        Route::post('/categories/{category}/delete', [AdminDashboardController::class, 'categoriesDestroy'])->name('admin.categories.destroy');

        // User & Staff Management CRUD
        Route::get('/users', [AdminDashboardController::class, 'usersIndex'])->name('admin.users.index');
        Route::post('/users/store-staff', [AdminDashboardController::class, 'usersStoreStaff'])->name('admin.users.store_staff');
        Route::post('/users/{user}/update', [AdminDashboardController::class, 'usersUpdate'])->name('admin.users.update');
        Route::post('/users/{id}/delete', [AdminDashboardController::class, 'usersDestroy'])->name('admin.users.destroy');

        // Order deletion line - restricted to Full Admin
        Route::post('/orders/{order}/delete', [AdminDashboardController::class, 'ordersDestroy'])->name('admin.orders.destroy');

        // Settings (Account & GCash Management)
        Route::get('/settings', [AdminDashboardController::class, 'settingsShow'])->name('admin.settings');
        Route::post('/settings/profile', [AdminDashboardController::class, 'settingsUpdateProfile'])->name('admin.settings.profile');
        Route::post('/settings/password', [AdminDashboardController::class, 'settingsUpdatePassword'])->name('admin.settings.password');
        Route::post('/settings/gcash', [AdminDashboardController::class, 'settingsUpdateGcash'])->name('admin.settings.gcash');
        Route::post('/settings/open-hours', [AdminDashboardController::class, 'settingsUpdateOpenHours'])->name('admin.settings.open_hours');
    });
});
