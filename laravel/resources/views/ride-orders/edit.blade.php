@extends('layouts.app')

@section('content')
    <h2>Редагувати замовлення</h2>
    <form action="{{ route('ride-orders.update', $rideOrder->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Клієнт</label>
            <select name="client_id" class="form-control">
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ $client->id == $rideOrder->client_id ? 'selected' : '' }}>{{ $client->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Водій</label>
            <select name="driver_id" class="form-control">
                @foreach($drivers as $driver)
                    <option value="{{ $driver->id }}" {{ $driver->id == $rideOrder->driver_id ? 'selected' : '' }}>{{ $driver->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Маршрут</label>
            <select name="route_id" class="form-control">
                @foreach($routes as $route)
                    <option value="{{ $route->id }}" {{ $route->id == $rideOrder->route_id ? 'selected' : '' }}>{{ $route->start_location }} – {{ $route->end_location }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Статус</label>
            <select name="status" class="form-control">
                <option value="new" {{ $rideOrder->status == 'new' ? 'selected' : '' }}>New</option>
                <option value="in_progress" {{ $rideOrder->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ $rideOrder->status == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Оновити</button>
    </form>
@endsection
