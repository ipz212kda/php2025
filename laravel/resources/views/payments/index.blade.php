@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Платежі</h1>

    <form method="GET" class="mb-4">
        <input type="text" name="id" placeholder="ID" value="{{ request('id') }}">
        <input type="text" name="ride_order_id" placeholder="Замовлення ID" value="{{ request('ride_order_id') }}">
        <input type="text" name="amount" placeholder="Сума" value="{{ request('amount') }}">
        <input type="text" name="payment_method" placeholder="Метод оплати" value="{{ request('payment_method') }}">
        <input type="date" name="paid_at" value="{{ request('paid_at') }}">
        <select name="itemsPerPage" onchange="this.form.submit()">
            <option value="5" {{ request('itemsPerPage') == 5 ? 'selected' : '' }}>5</option>
            <option value="10" {{ request('itemsPerPage') == 10 ? 'selected' : '' }}>10</option>
            <option value="20" {{ request('itemsPerPage') == 20 ? 'selected' : '' }}>20</option>
        </select>
        <button type="submit">Фільтрувати</button>
    </form>
    <a href="{{ route('payments.create') }}" class="btn btn-primary mb-3">Додати платіж</a>
    <table border="1" class="table table-striped">
        <thead>
            <tr><th>ID</th><th>Замовлення</th><th>Сума</th><th>Метод</th><th>Дата</th><th>Дії</th></tr>
        </thead>
        <tbody>
            @forelse ($payments as $payment)
                <tr>
                    <td>{{ $payment->id }}</td>
                    <td>{{ $payment->ride_order_id }}</td>
                    <td>{{ $payment->amount }}</td>
                    <td>{{ $payment->payment_method }}</td>
                    <td>{{ $payment->paid_at }}</td>
                    <td>
                        <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-sm btn-warning">Редагувати</a>
                        <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Видалити?')">Видалити</button>
                        </form>
                    </td>
                </tr>
            @empty <tr><td colspan="5">Немає платежів</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $payments->withQueryString()->links() }}
</div>
@endsection