@extends('layouts.app')

@section('content')
    <h2>Додати водія</h2>
    <form action="{{ route('drivers.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Ім’я</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Модель авто</label>
            <input type="text" name="car_model" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Номер авто</label>
            <input type="text" name="license_plate" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Телефон</label>
            <input type="text" name="phone" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Зберегти</button>
    </form>
@endsection