<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\RideOrderController;
use App\Http\Controllers\PaymentController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', [TestController::class, 'test']);
Route::resource('products', ProductController::class);

// Водії
Route::get('/drivers', [DriverController::class, 'index'])->name('drivers.index');
Route::get('/drivers/create', [DriverController::class, 'create'])->name('drivers.create');
Route::post('/drivers', [DriverController::class, 'store'])->name('drivers.store');
Route::get('/drivers/{driver}/edit', [DriverController::class, 'edit'])->name('drivers.edit');
Route::put('/drivers/{driver}', [DriverController::class, 'update'])->name('drivers.update');
Route::delete('/drivers/{driver}', [DriverController::class, 'destroy'])->name('drivers.destroy');

// Клієнти
Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

// Маршрути
Route::get('/routes', [RouteController::class, 'index'])->name('routes.index');
Route::get('/routes/create', [RouteController::class, 'create'])->name('routes.create');
Route::post('/routes', [RouteController::class, 'store'])->name('routes.store');
Route::get('/routes/{route}/edit', [RouteController::class, 'edit'])->name('routes.edit');
Route::put('/routes/{route}', [RouteController::class, 'update'])->name('routes.update');
Route::delete('/routes/{route}', [RouteController::class, 'destroy'])->name('routes.destroy');

// Замовлення поїздки
Route::get('/ride-orders', [RideOrderController::class, 'index'])->name('ride-orders.index');
Route::get('/ride-orders/create', [RideOrderController::class, 'create'])->name('ride-orders.create');
Route::post('/ride-orders', [RideOrderController::class, 'store'])->name('ride-orders.store');
Route::get('/ride-orders/{rideOrder}/edit', [RideOrderController::class, 'edit'])->name('ride-orders.edit');
Route::put('/ride-orders/{rideOrder}', [RideOrderController::class, 'update'])->name('ride-orders.update');
Route::delete('/ride-orders/{rideOrder}', [RideOrderController::class, 'destroy'])->name('ride-orders.destroy');

// Оплати
Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
