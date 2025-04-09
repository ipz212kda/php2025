@extends('layouts.app')

@section('content')
    <a href="{{ route('payments.create') }}" class="btn btn-primary mb-3">+ Додати платіж</a>
    <table class="table">
        <thead>
            <tr>
                <th>Замовлення</th>
                <th>Сума</th>
                <th>Метод оплати</th>
                <th>Час платежу</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->rideOrder->client->name }} – {{ $payment->rideOrder->route->start_location }} → {{ $payment->rideOrder->route->end_location }}</td>
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
            @endforeach
        </tbody>
    </table>
@endsection
