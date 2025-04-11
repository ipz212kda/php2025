<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\RideOrder;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::query();

        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        if ($request->filled('ride_order_id')) {
            $query->where('ride_order_id', $request->ride_order_id);
        }

        if ($request->filled('amount')) {
            $query->where('amount', $request->amount);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', 'like', '%' . $request->payment_method . '%');
        }

        if ($request->filled('paid_at')) {
            $query->whereDate('paid_at', $request->paid_at);
        }

        $itemsPerPage = $request->input('itemsPerPage', 10);
        $payments = $query->paginate($itemsPerPage)->appends($request->all());

        return view('payments.index', compact('payments'));
    }

    public function create() {
        $rideOrders = RideOrder::all();
        return view('payments.create', compact('rideOrders'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'ride_order_id' => 'required|exists:ride_orders,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,online',
            'paid_at' => 'required|date',
        ]);

        Payment::create($data);
        return redirect()->route('payments.index'); 
    }

    public function edit($id) {
        $payment = Payment::findOrFail($id);
        return view('payments.edit', compact('payment'));
    }

    public function show($id) {
        return Payment::with('rideOrder')->findOrFail($id);
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'ride_order_id' => 'required|exists:ride_orders,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,online',
            'paid_at' => 'required|date',
        ]);

        $payment->update($validated);
        return redirect()->route('payments.index');
    }

    public function destroy($id) {
        Payment::destroy($id);
        return redirect()->route('payments.index');
    }
}