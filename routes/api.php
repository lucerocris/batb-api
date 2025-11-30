<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Http\Controllers\CartController;

/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/



use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryMovementController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\ExportController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot-password', [ResetPasswordController::class, 'passwordEmail'])->middleware('guest')->name('password.email');
Route::post('/reset-password', [ResetPasswordController::class, 'passwordUpdate'])->middleware('guest')->name('password.update');

Route::put('/inventory-movements/adjustStock', [InventoryMovementController::class, 'adjustStock']);

Route::prefix('admin')->group(function(){
    Route::get('/products', [ProductController::class, 'showAll']);
    Route::post('/create-product', [ProductController::class, 'addProduct']);
    Route::get('/products/trashed', [ProductController::class, 'trashed']);
    Route::patch('/products/{product}/restore', [ProductController::class, 'restoreProduct']);
    Route::get('/categories', [CategoryController::class, 'showAll']);
    Route::get('/categories/trashed', [CategoryController::class, 'trashed']);
    Route::get('/categories/{category}/restore', [CategoryController::class, 'restoreCategory']);

});


Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

Route::prefix('export')->group(function() {
    Route::get('/products', [ExportController::class, 'exportProducts']);
    Route::get('/orders', [ExportController::class, 'exportOrders']);
});


//USERS
//  GET|HEAD        api/users  users.index › UserController@index  (restrict)
//   POST            api/users  users.store › UserController@store (restrict)
//   GET|HEAD        api/users/{user}  users.show › UserController@show  (restrict)
//   PUT|PATCH       api/users/{user} users.update › UserController@update  (restrict)
//   DELETE          api/users/{user} users.destroy › UserController@destroy  (restrict)


//ADRESSES
// GET|HEAD        api/addresses addresses.index › AddressController@index (restrict)
//   POST            api/addresses addresses.store › AddressController@store (restrict)
//   GET|HEAD        api/addresses/{address} addresses.show › AddressController@show (restrict)
//   PUT|PATCH       api/addresses/{address} addresses.update › AddressController@update (restrict)
//   DELETE          api/addresses/{address} addresses.destroy › AddressController@destroy


//protected routes
Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::apiResource('orders', OrderController::class);
Route::apiResource('users', UserController::class);

Route::apiResource('products', ProductController::class)->except(['update']);
Route::post("/products/{product}", [ProductController::class, "update"]);
Route::apiResource('addresses', AddressController::class);

Route::apiResource('order-items', OrderItemController::class);
Route::apiResource('product-variants', ProductVariantController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('inventory-movements', InventoryMovementController::class);
