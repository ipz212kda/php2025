@extends('layouts.app')

@section('content')
    <h2>Редагувати маршрут</h2>
    <form action="{{ route('routes.update', $route->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Початок</label>
            <input type="text" name="start_location" value="{{ $route->start_location }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Кінець</label>
            <input type="text" name="end_location" value="{{ $route->end_location }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Відстань (км)</label>
            <input type="number" name="distance_km" value="{{ $route->distance_km }}" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Оновити</button>
    </form>
@endsection