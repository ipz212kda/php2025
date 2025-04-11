<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index(Request $request)
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
        $drivers = $query->paginate($itemsPerPage)->appends($request->all());

        return view('drivers.index', compact('drivers'));
    }

    public function create() {
        return view('drivers.create');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required',
            'car_model' => 'required',
            'license_plate' => 'required',
            'phone' => 'required'
        ]);
        
        Driver::create($request->all());
        return redirect()->route('drivers.index');
    }
    
    public function edit($id) {
        $driver = Driver::findOrFail($id);
        return view('drivers.edit', compact('driver'));
    }

    public function show($id) {
        return Driver::findOrFail($id);
    }

    public function update(Request $request, $id) {
        $driver = Driver::findOrFail($id);
        $driver->update($request->all());
        return redirect()->route('drivers.index');
    }

    public function destroy($id) {
        Driver::destroy($id);
        return redirect()->route('drivers.index');
    }
}