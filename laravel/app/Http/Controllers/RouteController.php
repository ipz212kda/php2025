<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index(Request $request)
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
        $routes = $query->paginate($itemsPerPage)->appends($request->all());

        return view('routes.index', compact('routes'));
    }

    public function create() {
        return view('routes.create');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'start_location' => 'required',
            'end_location' => 'required',
            'distance_km' => 'required|numeric'
        ]);

        Route::create($data);
        return redirect()->route('routes.index');
    }

    public function edit($id) {
        $route = Route::findOrFail($id);
        return view('routes.edit', compact('route'));
    }

    public function show($id) {
        return Route::findOrFail($id);
    }

    public function update(Request $request, $id) {
        $data = $request->validate([
            'start_location' => 'required',
            'end_location' => 'required',
            'distance_km' => 'required|numeric'
        ]);

        $route = Route::findOrFail($id);
        $route->update($data);
        return redirect()->route('routes.index');
    }

    public function destroy($id) {
        Route::destroy($id);
        return redirect()->route('routes.index');  
    }
}