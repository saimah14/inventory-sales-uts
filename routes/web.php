<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierController;
use App\Http\Middleware\AdminOnly;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Khusus Admin (dicek di server lewat middleware AdminOnly)
    Route::middleware(AdminOnly::class)->group(function () {
        Route::get('/users', fn () => view('users', ['users' => User::all()]))->name('users.index');
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('suppliers', SupplierController::class)->except('show');
        Route::resource('products', ProductController::class)->except(['index', 'show']);
        Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
        Route::post('/purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');
    });

    // Staff dan Admin
    Route::resource('products', ProductController::class)->only(['index', 'show']);
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);
});
