<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Response;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OfferController;

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

// Route::fallback(function () {
//     return response()->json(['message' => '404 not found'], Response::HTTP_NOT_FOUND);
// });

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function () {    
    // Orders routes with role-based access
    Route::group(['middleware' => 'role:supplier,store,admin,client'], function () {
        Route::resource('orders', OrderController::class)->except(['create', 'edit']);
    });

    // Offers routes with role-based access
    Route::group(['middleware' => 'role:supplier,store,admin'], function () {
        Route::resource('offers', OfferController::class)->except(['create', 'edit']);
        Route::get('/offers-by-user', [OfferController::class, 'index_by_user']);
        Route::get('/orders-by-user', [OrderController::class, 'index_for_courier']);
        

    });
    
    // Offers routes with role-based access
    Route::group(['middleware' => 'role:supplier,store,admin'], function () {
        Route::resource('offers', OfferController::class)->except(['create', 'edit']);
    });

    // You can return a response here in a specific route handler.
    Route::post('logout', [UserController::class, 'logout']);

    // Example of returning a JSON response
    Route::get('check-token', function () {
        return response()->json(['message' => 'Token is valid.']);
    });
});
