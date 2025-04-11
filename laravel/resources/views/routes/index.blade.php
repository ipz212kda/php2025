@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Маршрути</h1>

    <form method="GET" class="mb-4">
        <input type="text" name="id" placeholder="ID" value="{{ request('id') }}">
        <input type="text" name="start_location" placeholder="Звідки" value="{{ request('start_location') }}">
        <input type="text" name="end_location" placeholder="Куди" value="{{ request('end_location') }}">
        <input type="text" name="distance_km" placeholder="Відстань (км)" value="{{ request('distance_km') }}">
        <select name="itemsPerPage" onchange="this.form.submit()">
            <option value="5" {{ request('itemsPerPage') == 5 ? 'selected' : '' }}>5</option>
            <option value="10" {{ request('itemsPerPage') == 10 ? 'selected' : '' }}>10</option>
            <option value="20" {{ request('itemsPerPage') == 20 ? 'selected' : '' }}>20</option>
        </select>
        <button type="submit">Фільтрувати</button>
    </form>
    <a href="{{ route('routes.create') }}" class="btn btn-primary mb-3">Додати маршрут</a>
    <table border="1" class="table table-striped">
        <thead>
            <tr><th>ID</th><th>Звідки</th><th>Куди</th><th>Відстань</th></tr>
        </thead>
        <tbody>
            @forelse ($routes as $route)
                <tr>
                    <td>{{ $route->id }}</td>
                    <td>{{ $route->start_location }}</td>
                    <td>{{ $route->end_location }}</td>
                    <td>{{ $route->distance_km }} км</td>
                    <td>
                        <a href="{{ route('routes.edit', $route->id) }}" class="btn btn-sm btn-warning">Редагувати</a>
                        <form action="{{ route('routes.destroy', $route->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Видалити?')">Видалити</button>
                        </form>
                    </td>
                </tr>
            @empty <tr><td colspan="4">Маршрутів не знайдено</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $routes->withQueryString()->links() }}
</div>
@endsection