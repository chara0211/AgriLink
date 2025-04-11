<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Middleware\EnsureUserIsAuthenticated;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::apiResource('users', UserController::class);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('products', [ProductController::class, 'store']);
Route::get('products', [ProductController::class, 'index']); // Add this line to fetch products
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::get('/users', [UserController::class, 'index']);  // No 'auth' middleware

// In routes/api.php
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::get('posts', [PostController::class, 'index']);
Route::post('posts', [PostController::class, 'store']);

Route::get('comments', [CommentController::class, 'index']);
Route::post('/comments', [CommentController::class, 'store']);

// Route pour mettre à jour un post
Route::put('/posts/{id}', [PostController::class, 'update']);
// Route pour supprimer un post
Route::delete('/posts/{id}', [PostController::class, 'destroy']);

Route::get('/products/user/{farmerId}', [ProductController::class, 'getProductsByUser']);

Route::get('/cart', [CartController::class, 'index']);
    
    // Ajouter un produit au panier
Route::post('/cart/add', [CartController::class, 'addToCart']);


Route::get('/orders', [OrderController::class, 'index']);
Route::post('place-order', [CartController::class, 'placeOrder']);

Route::get('/farmer/{farmer_id}/orders', [CartController::class, 'getOrdersForFarmer']);

Route::put('/orders/{order_id}/validate', [OrderController::class, 'validateOrder']);

Route::put('/users/{user_id}/update-vip', [UserController::class, 'updateVipStatus']);
Route::get('/farmer-stats', [OrderController::class, 'getFarmerStatistics']);
Route::get('/notifications/{userId}', [AuthController::class, 'getNotifications']);

Route::get('orders/notifications/{farmer_id}', [CartController::class, 'getOrderNotificationsForFarmer']);
Route::get('/client/{client_id}/order-notifications', [OrderController::class, 'getOrderNotificationsForClient']);
// Dashboard routes with role-based middleware
Route::middleware([EnsureUserIsAuthenticated::class])->group(function () {
    Route::get('/admin/dashboard', function () {
        return response()->json(['message' => 'Welcome to Admin Dashboard']);
    })->middleware([RoleMiddleware::class, 'role:admin']);

    Route::get('/farmer/dashboard', function () {
        return response()->json(['message' => 'Welcome to Farmer Dashboard']);
    })->middleware([RoleMiddleware::class, 'role:farmer']);

    Route::get('/client/dashboard', function () {
        return response()->json(['message' => 'Welcome to Client Dashboard']);
    })->middleware([RoleMiddleware::class, 'role:client']);
});