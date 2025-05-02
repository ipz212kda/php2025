<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\RideOrderController;
use App\Http\Controllers\PaymentController;

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});


Route::group(['middleware' => 'auth:api'], function () {

    Route::group(['prefix' => 'auth'], function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });


    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{product}', [ProductController::class, 'show']);


    Route::get('routes', [RouteController::class, 'index']);
    Route::get('routes/{route}', [RouteController::class, 'show']);


    Route::get('ride-orders', [RideOrderController::class, 'index']);
    Route::get('ride-orders/{rideOrder}', [RideOrderController::class, 'show']);
    Route::post('ride-orders', [RideOrderController::class, 'store']);


    Route::group(['middleware' => 'role:client'], function () {

        Route::put('ride-orders/{rideOrder}', [RideOrderController::class, 'update']);
        Route::delete('ride-orders/{rideOrder}', [RideOrderController::class, 'destroy']);
        

        Route::post('payments', [PaymentController::class, 'store']);
        Route::get('payments', [PaymentController::class, 'index']);
    });


    Route::group(['middleware' => 'role:manager,admin'], function () {

        Route::post('products', [ProductController::class, 'store']);
        Route::put('products/{product}', [ProductController::class, 'update']);
        Route::delete('products/{product}', [ProductController::class, 'destroy']);
        
        Route::post('routes', [RouteController::class, 'store']);
        Route::put('routes/{route}', [RouteController::class, 'update']);
        Route::delete('routes/{route}', [RouteController::class, 'destroy']);
        

        Route::resource('drivers', DriverController::class);
        

        Route::put('ride-orders/{rideOrder}/status', [RideOrderController::class, 'updateStatus']);
        

        Route::get('payments', [PaymentController::class, 'index']);
        Route::get('payments/{payment}', [PaymentController::class, 'show']);
    });

    Route::group(['middleware' => 'role:admin'], function () {

        Route::resource('clients', ClientController::class);
        

        Route::put('payments/{payment}', [PaymentController::class, 'update']);
        Route::delete('payments/{payment}', [PaymentController::class, 'destroy']);
        

        Route::delete('users/{user}', [AuthController::class, 'destroy']);
    });
});