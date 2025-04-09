@extends('layouts.app')

@section('content')
    <h2>Додати замовлення</h2>
    <form action="{{ route('ride-orders.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Клієнт</label>
            <select name="client_id" class="form-control">
                @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Водій</label>
            <select name="driver_id" class="form-control">
                @foreach($drivers as $driver)
                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Маршрут</label>
            <select name="route_id" class="form-control">
                @foreach($routes as $route)
                    <option value="{{ $route->id }}">{{ $route->start_location }} – {{ $route->end_location }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Статус</label>
            <select name="status" class="form-control">
                <option value="new">New</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Зберегти</button>
    </form>
@endsection
