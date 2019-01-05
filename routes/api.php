<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * Buyers
 */
Route::resource('buyers', 'Buyer\BuyerController', ['only' => ['index', 'show']]);
Route::resource('buyers.orders', 'Buyer\BuyerOrderController', ['only' => ['index']]);
Route::resource('buyers.products', 'Buyer\BuyerProductController', ['only' => ['index']]);
Route::resource('buyers.sellers', 'Buyer\BuyerSellerController', ['only' => ['index']]);
Route::resource('buyers.categories', 'Buyer\BuyerCategoryController', ['only' => ['index']]);
Route::resource('buyers.carts', 'Buyer\BuyerCartController', ['only' => ['index']]);
Route::resource('buyers.transactions', 'Buyer\BuyerAddOrderController', ['only' => ['store']]);

/**
 * Categories
 */
Route::resource('categories', 'Category\CategoryController', ['except' => ['create', 'edit']]);
Route::resource('categories.products', 'Category\CategoryProductController', ['only' => ['index']]);
Route::resource('categories.sellers', 'Category\CategorySellerController', ['only' => ['index']]);
Route::resource('categories.buyers', 'Category\CategoryBuyerController', ['only' => ['index']]);
Route::resource('categories.carts', 'Category\CategoryCartController', ['only' => ['index']]);
Route::resource('categories.orders', 'Category\CategoryOrderController', ['only' => ['index']]);

/**
 * Products
 */
Route::resource('products', 'Product\ProductController', ['only' => ['index', 'show']]);
Route::resource('products.categories', 'Product\ProductCategoryController', ['only' => ['index', 'update', 'destroy']]);
Route::resource('products.buyers', 'Product\ProductBuyerController', ['only' => ['index']]);
Route::resource('products.buyers.adds', 'Product\ProductBuyerAddToCartController', ['only' => ['store']]);
Route::delete('products/{product}/buyers/{buyer}/adds', 'Product\ProductBuyerAddToCartController@removeFromCart');
Route::resource('products.carts', 'Product\ProductCartController', ['only' => ['index']]);
Route::resource('products.orders', 'Product\ProductOrderController', ['only' => ['index']]);

/**
 * Sellers
 */
Route::resource('sellers', 'Seller\SellerController', ['only' => ['index', 'show']]);
Route::resource('sellers.categories', 'Seller\SellerCategoryController', ['only' => ['index']]);
Route::resource('sellers.buyers', 'Seller\SellerBuyerController', ['only' => ['index']]);
Route::resource('sellers.products', 'Seller\SellerProductController', ['except' => ['create', 'show', 'edit']]);
Route::resource('sellers.carts', 'Seller\SellerCartController', ['only' => ['index']]);
Route::resource('sellers.orders', 'Seller\SellerOrderController', ['only' => ['index', 'update', 'destroy']]);

/**
 * Carts
 */
Route::resource('carts', 'Cart\CartController', ['only' => ['index', 'show']]);
Route::resource('carts.categories', 'Cart\CartCategoryController', ['only' => ['index']]);
Route::resource('carts.sellers', 'Cart\CartSellerController', ['only' => ['index']]);

/**
 * Orders
 */
Route::resource('orders', 'Order\OrderController', ['only' => ['index', 'show']]);
Route::resource('orders.categories', 'Order\OrderCategoryController', ['only' => ['index']]);
Route::resource('orders.sellers', 'Order\OrderSellerController', ['only' => ['index']]);
Route::resource('orders.products', 'Order\OrderProductController', ['only' => ['index']]);

/**
 * Users
 */
Route::resource('users', 'User\UserController', ['except' => ['create', 'edit']]);
Route::name('verify')->get('users/verify/{token}', 'User\UserController@verifyUser');
Route::name('resend')->get('users/{user}/resend', 'User\UserController@resend');

/**
 * Auth
 */
Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');