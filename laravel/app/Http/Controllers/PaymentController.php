<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\RideOrder;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function index(Request $request): JsonResponse
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
        $payments = $query->paginate($itemsPerPage);

        return response()->json([
            'status' => 'success',
            'data' => $payments
        ]);
    }

    public function store(Request $request): JsonResponse 
    {
        $data = $request->validate([
            'ride_order_id' => 'required|exists:ride_orders,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,online',
            'paid_at' => 'required|date',
        ]);

        $payment = Payment::create($data);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Payment created successfully',
            'data' => $payment
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $payment = Payment::with('rideOrder')->findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'data' => $payment
        ]);
    }

    public function update(Request $request, Payment $payment): JsonResponse
    {
        $validated = $request->validate([
            'ride_order_id' => 'required|exists:ride_orders,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,online',
            'paid_at' => 'required|date',
        ]);

        $payment->update($validated);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Payment updated successfully',
            'data' => $payment
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Payment deleted successfully'
        ]);
    }
}