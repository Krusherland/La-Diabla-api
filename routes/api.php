<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\AdminMiddleware;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Public routes - Authentication
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Public routes - Categories
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// Public routes - Products
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/image/{filename}', [ProductController::class, 'getImage']);

// Public routes - Orders
Route::post('/orders', [OrderController::class, 'store']);
Route::post('/orders/track', [OrderController::class, 'track']);
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::get('/orders/user/{email}', [OrderController::class, 'getUserOrders']);

// Protected routes - Require authentication
Route::middleware([JwtMiddleware::class])->group(function () {
    // User profile
    Route::get('/auth/profile', [AuthController::class, 'profile']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
});

// Admin routes - Require authentication and admin role
Route::middleware([JwtMiddleware::class, AdminMiddleware::class])->group(function () {
    // Categories management
    Route::get('/admin/categories', [CategoryController::class, 'adminIndex']);
    Route::post('/admin/categories', [CategoryController::class, 'store']);
    Route::put('/admin/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/admin/categories/{id}', [CategoryController::class, 'destroy']);

    // Products management
    Route::get('/admin/products', [ProductController::class, 'adminIndex']);
    Route::post('/admin/products', [ProductController::class, 'store']);
    Route::put('/admin/products/{id}', [ProductController::class, 'update']);
    Route::delete('/admin/products/{id}', [ProductController::class, 'destroy']);

    // Orders management
    Route::get('/admin/orders', [OrderController::class, 'index']);
    Route::put('/admin/orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::delete('/admin/orders/{id}', [OrderController::class, 'destroy']);
    Route::get('/admin/orders/statistics', [OrderController::class, 'statistics']);
});
