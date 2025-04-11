@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Замовлення</h1>

    <form method="GET" class="mb-4">
        <input type="text" name="id" placeholder="ID" value="{{ request('id') }}">
        <input type="text" name="client_id" placeholder="Клієнт ID" value="{{ request('client_id') }}">
        <input type="text" name="driver_id" placeholder="Водій ID" value="{{ request('driver_id') }}">
        <input type="text" name="route_id" placeholder="Маршрут ID" value="{{ request('route_id') }}">
        <input type="text" name="status" placeholder="Статус" value="{{ request('status') }}">
        <select name="itemsPerPage" onchange="this.form.submit()">
            <option value="5" {{ request('itemsPerPage') == 5 ? 'selected' : '' }}>5</option>
            <option value="10" {{ request('itemsPerPage') == 10 ? 'selected' : '' }}>10</option>
            <option value="20" {{ request('itemsPerPage') == 20 ? 'selected' : '' }}>20</option>
        </select>
        <button type="submit">Фільтрувати</button>
    </form>
    <a href="{{ route('ride-orders.create') }}" class="btn btn-primary mb-3">Додати замовлення</a>
    <table border="1" class="table table-striped">
        <thead>
            <tr><th>ID</th><th>Клієнт</th><th>Водій</th><th>Маршрут</th><th>Статус</th><th>Дії</th></tr>
        </thead>
        <tbody>
            @forelse ($rideOrders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->client_id }}</td>
                    <td>{{ $order->driver_id }}</td>
                    <td>{{ $order->route_id }}</td>
                    <td>{{ $order->status }}</td>
                    <td>
                        <a href="{{ route('ride-orders.edit', $order->id) }}" class="btn btn-sm btn-warning">Редагувати</a>
                        <form action="{{ route('ride-orders.destroy', $order->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Видалити?')">Видалити</button>
                        </form>
                    </td>
                </tr>
            @empty <tr><td colspan="5">Немає замовлень</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $rideOrders->withQueryString()->links() }}
</div>
@endsection