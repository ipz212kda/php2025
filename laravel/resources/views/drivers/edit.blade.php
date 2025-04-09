@extends('layouts.app')

@section('content')
    <h2>Редагувати водія</h2>
    <form action="{{ route('drivers.update', $driver->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Ім’я</label>
            <input type="text" name="name" value="{{ $driver->name }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Модель авто</label>
            <input type="text" name="car_model" value="{{ $driver->car_model }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Номер авто</label>
            <input type="text" name="license_plate" value="{{ $driver->license_plate }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Телефон</label>
            <input type="text" name="phone" value="{{ $driver->phone }}" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Оновити</button>
    </form>
@endsection