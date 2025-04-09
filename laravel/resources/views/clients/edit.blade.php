@extends('layouts.app')

@section('content')
    <h2>Редагувати клієнта</h2>
    <form action="{{ route('clients.update', $client->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Ім’я</label>
            <input type="text" name="name" value="{{ $client->name }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ $client->email }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Телефон</label>
            <input type="text" name="phone" value="{{ $client->phone }}" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Оновити</button>
    </form>
@endsection