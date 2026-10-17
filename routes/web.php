<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminOnly;
use App\Http\Middleware\RoleOnly;
use App\Http\Middleware\StaffOnly;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route(auth()->check() && auth()->user()->isBuyer() ? 'products.index' : 'dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Khusus Admin (dicek di server)
    Route::middleware(AdminOnly::class)->group(function () {
        Route::resource('users', UserController::class)->except('show');
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('suppliers', SupplierController::class)->except('show');
        Route::resource('products', ProductController::class)->except(['index', 'show']);
        Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
        Route::post('/purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');
    });

    // Khusus Pembeli: keranjang dan checkout
    Route::middleware(RoleOnly::class . ':pembeli')->group(function () {
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart', [CartController::class, 'add'])->name('cart.add');
        Route::patch('/cart', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{productId}', [CartController::class, 'remove'])->name('cart.remove');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    });

    // Khusus Staff: terima/tolak pesanan dan buat penjualan langsung
    Route::middleware(RoleOnly::class . ':staff')->group(function () {
        Route::post('/orders/{order}/accept', [OrderController::class, 'accept'])->name('orders.accept');
        Route::post('/orders/{order}/reject', [OrderController::class, 'reject'])->name('orders.reject');
        Route::resource('sales', SaleController::class)->only(['create', 'store']);
    });

    // Admin dan Staff
    Route::middleware(StaffOnly::class)->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('sales', SaleController::class)->only(['index', 'show']);
        Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);
    });

    // Semua yang login (pembeli hanya melihat miliknya, dicek di controller)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::resource('products', ProductController::class)->only(['index', 'show']);
});
