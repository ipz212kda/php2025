@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Клієнти</h1>

    <form method="GET" class="mb-4">
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="text" name="id" placeholder="ID" value="{{ request('id') }}">
            <input type="text" name="name" placeholder="Ім’я" value="{{ request('name') }}">
            <input type="text" name="email" placeholder="Email" value="{{ request('email') }}">
            <input type="text" name="phone" placeholder="Телефон" value="{{ request('phone') }}">

            <select name="itemsPerPage" onchange="this.form.submit()">
                <option value="5" {{ request('itemsPerPage') == 5 ? 'selected' : '' }}>5</option>
                <option value="10" {{ request('itemsPerPage') == 10 ? 'selected' : '' }}>10</option>
                <option value="20" {{ request('itemsPerPage') == 20 ? 'selected' : '' }}>20</option>
            </select>

            <button type="submit">Фільтрувати</button>
        </div>
    </form>
    <a href="{{ route('clients.create') }}" class="btn btn-primary mb-3">Додати клієнта</a>
    <table border="1" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th><th>Ім’я</th><th>Email</th><th>Телефон</th><th>Дії</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($clients as $client)
                <tr>
                    <td>{{ $client->id }}</td>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->email }}</td>
                    <td>{{ $client->phone }}</td>
                    <td>
                        <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-sm btn-warning">Редагувати</a>
                        <form action="{{ route('clients.destroy', $client->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Видалити?')">Видалити</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">Немає результатів</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $clients->withQueryString()->links() }}
    </div>
</div>
@endsection