@extends('layouts.app')

@section('content')
    <h2>Додати платіж</h2>
    <form action="{{ route('payments.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Замовлення</label>
            <select name="ride_order_id" class="form-control">
                @foreach($rideOrders as $rideOrder)
                    <option value="{{ $rideOrder->id }}">{{ $rideOrder->client->name }} – {{ $rideOrder->route->start_location }} → {{ $rideOrder->route->end_location }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Сума</label>
            <input type="number" name="amount" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Метод оплати</label>
            <select name="payment_method" class="form-control">
                <option value="cash">Готівка</option>
                <option value="card">Картка</option>
                <option value="online">Онлайн</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Час платежу</label>
            <input type="datetime-local" name="paid_at" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Зберегти</button>
    </form>
@endsection
