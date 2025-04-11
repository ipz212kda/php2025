@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Водії</h1>

    <form method="GET" class="mb-4">
        <input type="text" name="id" placeholder="ID" value="{{ request('id') }}">
        <input type="text" name="name" placeholder="Ім’я" value="{{ request('name') }}">
        <input type="text" name="car_model" placeholder="Модель авто" value="{{ request('car_model') }}">
        <input type="text" name="license_plate" placeholder="Номер" value="{{ request('license_plate') }}">
        <input type="text" name="phone" placeholder="Телефон" value="{{ request('phone') }}">
        <select name="itemsPerPage" onchange="this.form.submit()">
            <option value="5" {{ request('itemsPerPage') == 5 ? 'selected' : '' }}>5</option>
            <option value="10" {{ request('itemsPerPage') == 10 ? 'selected' : '' }}>10</option>
            <option value="20" {{ request('itemsPerPage') == 20 ? 'selected' : '' }}>20</option>
        </select>
        <button type="submit">Фільтрувати</button>
    </form>
    <a href="{{ route('drivers.create') }}" class="btn btn-primary mb-3">Додати водія</a>
    <table border="1" class="table table-striped">
        <thead>
            <tr><th>ID</th><th>Ім’я</th><th>Авто</th><th>Номер</th><th>Телефон</th><th>Дії</th></tr>
        </thead>
        <tbody>
            @forelse ($drivers as $driver)
                <tr>
                    <td>{{ $driver->id }}</td>
                    <td>{{ $driver->name }}</td>
                    <td>{{ $driver->car_model }}</td>
                    <td>{{ $driver->license_plate }}</td>
                    <td>{{ $driver->phone }}</td>
                    <td>
                        <a href="{{ route('drivers.edit', $driver->id) }}" class="btn btn-sm btn-warning">Редагувати</a>
                        <form action="{{ route('drivers.destroy', $driver->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Видалити?')">Видалити</button>
                        </form>
                    </td>
                </tr>
            @empty <tr><td colspan="5">Немає водіїв</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $drivers->withQueryString()->links() }}
</div>
@endsection