@extends('layouts.app')

@section('content')
    <a href="{{ route('drivers.create') }}" class="btn btn-primary mb-3">+ Додати водія</a>
    <table class="table">
        <thead>
            <tr>
                <th>Ім’я</th>
                <th>Авто</th>
                <th>Номер</th>
                <th>Телефон</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            @foreach($drivers as $driver)
                <tr>
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
            @endforeach
        </tbody>
    </table>
@endsection