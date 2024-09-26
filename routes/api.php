<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerAddressController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::group([
    'prefix' => 'auth'
], function ($router) {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::group([
    'middleware' => 'jwt.verify',
    'prefix' => 'auth'
], function ($router) {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('edit-profile', [AuthController::class, 'profile']);
    Route::post('change-password', [AuthController::class, 'changePassword']);
    Route::post('my-orders', [AuthController::class, 'myorders']);
    Route::post('my-favorite', [AuthController::class, 'myfavorite']);
    Route::get('my-addresses', [AuthController::class, 'myaddress']);
    Route::get('refresh', [AuthController::class, 'refresh']);
});

Route::group([
    'prefix' => 'products'
], function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('detail/{id}', [ProductController::class, 'detail']);
});
Route::group([
    'prefix' => 'categories'
], function () {
    Route::get('/', [ProductController::class, 'categories']);
    Route::get('/{id}', [ProductController::class, 'category']);
    Route::get('/subcategory_products/{id}', [ProductController::class, 'all_products']);
    Route::get('/products/{category_id}/{subcategory_id}', [ProductController::class, 'category_products']);
});


Route::group([
    'middleware' => 'jwt.verify',
    'prefix' => 'cart'
], function () {
    Route::post('add_to_cart', [CartController::class, 'add']);
//    Route::post('show-my-cart', [CartController::class, 'show']);
    Route::post('cart_detailes', [CartController::class, 'get_all_products_in_cart']);
    Route::post('update/{productId}/{skuId}', [CartController::class, 'update']);
    Route::post('delete/{productId}/{skuId}', [CartController::class, 'delete']);

});

Route::group([
    'middleware' => 'jwt.verify',
    'prefix' => 'favorite'
], function () {
    Route::post('add_to_favorite', [FavoriteController::class, 'add']);
    Route::post('delete/{id}', [FavoriteController::class, 'delete']);
});

Route::group([
    'middleware' => 'jwt.verify',
    'prefix' => 'customer/address'
], function () {
    Route::post('create', [CustomerAddressController::class, 'create']);
    Route::post('delete/{id}', [CustomerAddressController::class, 'delete']);
    Route::post('edit/{id}', [CustomerAddressController::class, 'edit']);
    Route::post('update/{id}', [CustomerAddressController::class, 'update']);

});

Route::group([
    'middleware' => 'jwt.verify',
    'prefix' => 'order'
], function () {
    Route::post('create', [OrderController::class, 'create']);
    Route::post('delete/{id}', [OrderController::class, 'delete']);
    Route::post('edit/{id}', [OrderController::class, 'edit']);
    Route::post('update/{id}', [OrderController::class, 'update']);
    Route::group([
        'prefix' => 'detaile',
        'middleware' => 'jwt.verify',

    ], function () {
        Route::post('delete/{id}', [OrderDetailsController::class, 'delete']);
        Route::post('edit/{id}', [OrderDetailsController::class, 'edit']);
        Route::post('update/{id}', [OrderDetailsController::class, 'update']);

    });

});

Route::post('payment_types', [OrderController::class, 'payment_types'])->middleware('jwt.verify');

