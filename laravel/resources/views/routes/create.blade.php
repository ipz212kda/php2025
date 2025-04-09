@extends('layouts.app')

@section('content')
    <h2>Додати маршрут</h2>
    <form action="{{ route('routes.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Початок</label>
            <input type="text" name="start_location" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Кінець</label>
            <input type="text" name="end_location" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Відстань (км)</label>
            <input type="number" name="distance_km" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Зберегти</button>
    </form>
@endsection