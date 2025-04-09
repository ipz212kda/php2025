<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index() {
        $routes = Route::all();
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