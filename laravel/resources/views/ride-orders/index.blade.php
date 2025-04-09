@extends('layouts.app')

@section('content')
    <a href="{{ route('ride-orders.create') }}" class="btn btn-primary mb-3">+ Додати замовлення</a>
    <table class="table">
        <thead>
            <tr>
                <th>Клієнт</th>
                <th>Водій</th>
                <th>Маршрут</th>
                <th>Статус</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rideOrders as $rideOrder)
                <tr>
                    <td>{{ $rideOrder->client->name }}</td>
                    <td>{{ $rideOrder->driver->name }}</td>
                    <td>{{ $rideOrder->route->start_location }} – {{ $rideOrder->route->end_location }}</td>
                    <td>{{ $rideOrder->status }}</td>
                    <td>
                        <a href="{{ route('ride-orders.edit', $rideOrder->id) }}" class="btn btn-sm btn-warning">Редагувати</a>
                        <form action="{{ route('ride-orders.destroy', $rideOrder->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Видалити?')">Видалити</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
