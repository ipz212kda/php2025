<h1>Список товарів</h1>
<a href="{{ route('products.create') }}">Додати товар</a>

@foreach($products as $product)
    <p>
        <strong>{{ $product->name }}</strong> — {{ $product->price }} грн
        <a href="{{ route('products.edit', $product) }}">Редагувати</a>
        <form action="{{ route('products.destroy', $product) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit">Видалити</button>
        </form>
    </p>
@endforeach