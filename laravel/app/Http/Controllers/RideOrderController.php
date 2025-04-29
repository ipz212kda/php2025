<?php

namespace App\Http\Controllers;

use App\Models\RideOrder;
use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\Client;
use App\Models\Route;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;

class RideOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = RideOrder::query();

        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        if ($request->filled('route_id')) {
            $query->where('route_id', $request->route_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $itemsPerPage = $request->input('itemsPerPage', 10);
        $rideOrders = $query->paginate($itemsPerPage);

        return response()->json([
            'status' => 'success',
            'data' => $rideOrders
        ]);
    }

    public function store(Request $request): JsonResponse 
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'driver_id' => 'required|exists:drivers,id',
            'route_id' => 'required|exists:routes,id',
            'status' => 'required|in:new,in_progress,completed'
        ]);

        $rideOrder = RideOrder::create($data);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Ride order created successfully',
            'data' => $rideOrder
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $rideOrder = RideOrder::with(['client', 'driver', 'route', 'payment'])->findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'data' => $rideOrder
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'driver_id' => 'required|exists:drivers,id',
            'route_id' => 'required|exists:routes,id',
            'status' => 'required|in:new,in_progress,completed'
        ]);

        $rideOrder = RideOrder::findOrFail($id);
        $rideOrder->update($validated);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Ride order updated successfully',
            'data' => $rideOrder
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $rideOrder = RideOrder::findOrFail($id);
        $rideOrder->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Ride order deleted successfully'
        ]);
    }

    /**
     * Update the status of a ride order (Manager and Admin only).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RideOrder  $rideOrder
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, RideOrder $rideOrder)
    {
        $request->validate([
            'status' => 'required|string|in:pending,confirmed,in_progress,completed,cancelled',
        ]);

        $rideOrder->status = $request->status;
        $rideOrder->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Ride order status updated successfully',
            'data' => $rideOrder
        ]);
    }
}