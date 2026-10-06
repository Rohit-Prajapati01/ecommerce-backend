<?php

use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\BrandsController;
use App\Http\Controllers\admin\CategoriesController;
use App\Http\Controllers\admin\OrderController as AdminOrderController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ShippingController;
use App\Http\Controllers\admin\SizeController;
use App\Http\Controllers\front\AccountController;
use App\Http\Controllers\front\OrderController;
use App\Http\Controllers\front\ProductController as FrontProductController;
use App\Http\Controllers\TempImageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\front\ShippingController as FrontShippingController;


Route::post('/admin/login', [AuthController::class, 'authenticate']);
Route::get('get-latest-products', [FrontProductController::class, 'latestProducts']);
Route::get('get-featured-products', [FrontProductController::class, 'featuredProducts']);
Route::get('get-categories', [FrontProductController::class, 'getCategories']);
Route::get('get-brands', [FrontProductController::class, 'getBrands']);
Route::get('get-products', [FrontProductController::class, 'getProducts']);
Route::post('register', [AccountController::class, 'register']);
Route::post('login', [AccountController::class, 'authenticate']);
Route::get('get-product/{id}', [FrontProductController::class, 'getProduct']); //Single product
Route::get('get-shipping-charge', [FrontShippingController::class, 'getShippingCharge']);

Route::group(['middleware' => ['auth:sanctum', 'checkUserRole']], function () {
    Route::post('save-order', [OrderController::class, 'saveOrder']);
    Route::get('get-order-details/{id}', [AccountController::class, 'getOrderDetails']);
    Route::get('get-orders', [AccountController::class, 'getOrders']);
    Route::post('update-profile', [AccountController::class, 'updateProfile']);
    Route::get('get-account-details', [AccountController::class, 'getAccountDetails']);
    Route::post('create-payment-intent', [OrderController::class, 'createPaymentIntent']);
});

Route::group(['middleware' => ['auth:sanctum', 'checkAdminRole']], function () {
    Route::resource('categories', CategoriesController::class);
    Route::resource('brands', BrandsController::class);
    Route::resource('products', ProductController::class);
    Route::get('sizes', [SizeController::class, 'index']);
    Route::post('temp-image', [TempImageController::class, 'store']);
    Route::post('save-product-image', [ProductController::class, 'saveProductImage']);
    Route::get('change-product-image', [ProductController::class, 'updateDefaultImage']);
    Route::delete('delete-product-image/{id}', [ProductController::class, 'deleteProductImage']);
    Route::get('orders', [AdminOrderController::class, 'index']);
    Route::get('orders/{id}', [AdminOrderController::class, 'details']);
    Route::post('update-order/{id}', [AdminOrderController::class, 'updateOrder']);
    Route::get('shipping-charge', [ShippingController::class, 'getShippingCharge']);
    Route::post('update-shipping-charge', [ShippingController::class, 'updateShippingCharge']);
});







// Categories Routes

// Route::get('categories', [CategoriesController::class, 'index']);
// Route::get('categories/{id}', [CategoriesController::class, 'show']);
// Route::put('categories/{id}', [CategoriesController::class, 'update']);
// Route::post('categories', [CategoriesController::class, 'store']);
// Route::delete('categories/{id}', [CategoriesController::class, 'destroy']);




// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
