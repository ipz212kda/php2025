<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index() {
        $payments = Payment::all();
        return view('payments.index', compact('payments'));
    }

    public function create() {
        return view('payments.create');
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