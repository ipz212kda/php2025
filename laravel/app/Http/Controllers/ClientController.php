<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
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
        $clients = $query->paginate($itemsPerPage)->appends($request->all());

        return view('clients.index', compact('clients'));
    }

    public function create() {
        return view('clients.create');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:clients',
            'phone' => 'required'
        ]);
        Client::create($data);
        return redirect()->route('clients.index');
    }

    public function edit($id) {
        $client = Client::findOrFail($id);
        return view('clients.edit', compact('client'));
    }

    public function show($id) {
        return Client::findOrFail($id);
    }

    public function update(Request $request, $id) {
        $client = Client::findOrFail($id);
        $client->update($request->all());
        return redirect()->route('clients.index');
    }

    public function destroy($id) {
        Client::destroy($id);
        return redirect()->route('clients.index');
    }
}