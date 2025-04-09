<?php

namespace App\Http\Controllers;

use App\Models\RideOrder;
use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\Client;
use App\Models\Route;
use App\Models\Payment;


class RideOrderController extends Controller
{
    public function index() {
        $rideOrders = RideOrder::with(['driver', 'client', 'route'])->get();
        return view('ride-orders.index', compact('rideOrders'));
    }

    public function create() {
        $drivers = Driver::all();
        $clients = Client::all();
        $routes = Route::all(); 

        return view('ride-orders.create', compact('drivers', 'clients', 'routes'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'driver_id' => 'required|exists:drivers,id',
            'route_id' => 'required|exists:routes,id',
            'status' => 'required|in:new,in_progress,completed'
        ]);

        RideOrder::create($data);
        return redirect()->route('ride-orders.index');
    }

    public function show($id) {
        return RideOrder::with(['client', 'driver', 'route', 'payment'])->findOrFail($id);
    }

    public function edit($id) {
        $rideOrder = RideOrder::findOrFail($id);
        $drivers = Driver::all();
        $clients = Client::all();
        $routes = Route::all();

        return view('ride-orders.edit', compact('rideOrder', 'drivers', 'clients', 'routes'));
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'driver_id' => 'required|exists:drivers,id',
            'route_id' => 'required|exists:routes,id',
            'status' => 'required|in:new,in_progress,completed'
        ]);

        $rideOrder = RideOrder::findOrFail($id);
        $rideOrder->update($validated);
        return redirect()->route('ride-orders.index');
    }

    public function destroy($id) {
        RideOrder::destroy($id);
        return redirect()->route('ride-orders.index');
    }
}