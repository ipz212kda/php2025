@extends('layouts.app')

@section('content')
    <a href="{{ route('routes.create') }}" class="btn btn-primary mb-3">+ Додати маршрут</a>
    <table class="table">
        <thead>
            <tr>
                <th>Початок</th>
                <th>Кінець</th>
                <th>Відстань (км)</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            @foreach($routes as $route)
                <tr>
                    <td>{{ $route->start_location }}</td>
                    <td>{{ $route->end_location }}</td>
                    <td>{{ $route->distance_km }}</td>
                    <td>
                        <a href="{{ route('routes.edit', $route->id) }}" class="btn btn-sm btn-warning">Редагувати</a>
                        <form action="{{ route('routes.destroy', $route->id) }}" method="POST" style="display:inline-block;">
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