<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DriverController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Driver::query();

        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('car_model')) {
            $query->where('car_model', 'like', '%' . $request->car_model . '%');
        }

        if ($request->filled('license_plate')) {
            $query->where('license_plate', 'like', '%' . $request->license_plate . '%');
        }

        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        $itemsPerPage = $request->input('itemsPerPage', 10);
        $drivers = $query->paginate($itemsPerPage);

        return response()->json([
            'status' => 'success',
            'data' => $drivers
        ]);
    }

    public function store(Request $request): JsonResponse 
    {
        $data = $request->validate([
            'name' => 'required',
            'car_model' => 'required',
            'license_plate' => 'required',
            'phone' => 'required'
        ]);
        
        $driver = Driver::create($request->all());
        
        return response()->json([
            'status' => 'success',
            'message' => 'Driver created successfully',
            'data' => $driver
        ], 201);
    }
    
    public function show($id): JsonResponse
    {
        $driver = Driver::findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'data' => $driver
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $driver = Driver::findOrFail($id);
        
        $data = $request->validate([
            'name' => 'required',
            'car_model' => 'required',
            'license_plate' => 'required',
            'phone' => 'required'
        ]);
        
        $driver->update($request->all());
        
        return response()->json([
            'status' => 'success',
            'message' => 'Driver updated successfully',
            'data' => $driver
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $driver = Driver::findOrFail($id);
        $driver->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Driver deleted successfully'
        ]);
    }
}