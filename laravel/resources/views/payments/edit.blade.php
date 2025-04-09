@extends('layouts.app')

@section('content')
    <h2>Редагувати платіж</h2>
    <form action="{{ route('payments.update', $payment->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Замовлення</label>
            <select name="ride_order_id" class="form-control">
                @foreach($rideOrders as $rideOrder)
                    <option value="{{ $rideOrder->id }}" {{ $rideOrder->id == $payment->ride_order_id ? 'selected' : '' }}>{{ $rideOrder->client->name }} – {{ $rideOrder->route->start_location }} → {{ $rideOrder->route->end_location }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Сума</label>
            <input type="number" name="amount" value="{{ $payment->amount }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Метод оплати</label>
            <select name="payment_method" class="form-control">
                <option value="cash" {{ $payment->payment_method == 'cash' ? 'selected' : '' }}>Готівка</option>
                <option value="card" {{ $payment->payment_method == 'card' ? 'selected' : '' }}>Картка</option>
                <option value="online" {{ $payment->payment_method == 'online' ? 'selected' : '' }}>Онлайн</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Час платежу</label>
            <input type="datetime-local" name="paid_at" value="{{ $payment->paid_at->format('Y-m-d\TH:i') }}" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Оновити</button>
    </form>
@endsection
