<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Client::query();

        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        $itemsPerPage = $request->input('itemsPerPage', 10);
        $clients = $query->paginate($itemsPerPage);

        return response()->json([
            'status' => 'success',
            'data' => $clients
        ]);
    }

    public function store(Request $request): JsonResponse 
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:clients',
            'phone' => 'required'
        ]);
        
        $client = Client::create($data);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Client created successfully',
            'data' => $client
        ], 201);
    }
    
    public function show($id): JsonResponse
    {
        $client = Client::findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'data' => $client
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $client = Client::findOrFail($id);
        
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:clients,email,' . $id,
            'phone' => 'required'
        ]);
        
        $client->update($data);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Client updated successfully',
            'data' => $client
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $client = Client::findOrFail($id);
        $client->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Client deleted successfully'
        ]);
    }
}