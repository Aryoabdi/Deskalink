<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\Auth\RegisteredUserController;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Authentication routes (handled by Laravel Breeze)
// Registrasi route
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');
    
    Route::post('register', [RegisteredUserController::class, 'store']);
});

// Authentication routes for login, logout handled by Laravel Breeze
require __DIR__.'/auth.php';

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Market routes
    Route::prefix('market')->group(function () {
        Route::get('/', [MarketController::class, 'index'])->name('market.index');
        Route::get('/product/{id}', [MarketController::class, 'show'])->name('market.show');
        Route::get('/cart', [MarketController::class, 'cart'])->name('market.cart');
        Route::post('/cart/add', [MarketController::class, 'addToCart'])->name('market.cart.add');
        Route::delete('/cart/remove', [MarketController::class, 'removeFromCart'])->name('market.cart.remove');
        Route::get('/checkout', [MarketController::class, 'checkout'])->name('market.checkout');
    });

    // Partner routes
    Route::middleware(['role:partner'])->prefix('partner')->group(function () {
        // Designs
        Route::resource('designs', DesignController::class);
        Route::post('designs/{design}/preview', [DesignController::class, 'addPreview'])->name('designs.preview.add');
        Route::delete('designs/{design}/preview/{preview}', [DesignController::class, 'removePreview'])->name('designs.preview.remove');

        // Services
        Route::resource('services', ServiceController::class);

        // Portfolio
        Route::resource('portfolio', PortfolioController::class);

        // Orders management
        Route::get('orders', [OrderController::class, 'partnerOrders'])->name('partner.orders');
    });

    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        // Dashboard
        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // User management
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/status', [UserController::class, 'updateStatus'])->name('users.status');

        // Content moderation
        Route::get('moderation', [ContentController::class, 'index'])->name('admin.moderation');
        Route::patch('moderation/{type}/{id}', [ContentController::class, 'moderate'])->name('admin.moderate');
        Route::get('moderation/logs', [ContentController::class, 'logs'])->name('admin.moderation.logs');

        // Reports
        Route::get('reports', [ContentController::class, 'reports'])->name('admin.reports');
        Route::patch('reports/{report}', [ContentController::class, 'handleReport'])->name('admin.reports.handle');

        // Orders & Transactions
        Route::get('orders', [OrderController::class, 'adminIndex'])->name('admin.orders');
        Route::get('transactions', [OrderController::class, 'transactions'])->name('admin.transactions');
    });

    // Client routes
    Route::middleware(['role:client'])->prefix('client')->group(function () {
        Route::get('orders', [OrderController::class, 'clientOrders'])->name('client.orders');
        Route::post('orders', [OrderController::class, 'store'])->name('client.orders.store');
    });

    // Common routes for all authenticated users
    Route::post('report/user/{user}', [UserController::class, 'report'])->name('user.report');
});
