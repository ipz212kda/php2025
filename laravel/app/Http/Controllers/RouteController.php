<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RouteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Route::query();

        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        if ($request->filled('start_location')) {
            $query->where('start_location', 'like', '%' . $request->start_location . '%');
        }

        if ($request->filled('end_location')) {
            $query->where('end_location', 'like', '%' . $request->end_location . '%');
        }

        if ($request->filled('distance_km')) {
            $query->where('distance_km', $request->distance_km);
        }

        $itemsPerPage = $request->input('itemsPerPage', 10);
        $routes = $query->paginate($itemsPerPage);

        return response()->json([
            'status' => 'success',
            'data' => $routes
        ]);
    }

    public function store(Request $request): JsonResponse 
    {
        $data = $request->validate([
            'start_location' => 'required',
            'end_location' => 'required',
            'distance_km' => 'required|numeric'
        ]);

        $route = Route::create($data);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Route created successfully',
            'data' => $route
        ], 201);
    }
    
    public function show($id): JsonResponse
    {
        $route = Route::findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'data' => $route
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $data = $request->validate([
            'start_location' => 'required',
            'end_location' => 'required',
            'distance_km' => 'required|numeric'
        ]);

        $route = Route::findOrFail($id);
        $route->update($data);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Route updated successfully',
            'data' => $route
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $route = Route::findOrFail($id);
        $route->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Route deleted successfully'
        ]);
    }
}