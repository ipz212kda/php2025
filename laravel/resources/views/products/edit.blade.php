<h1>Редагувати товар</h1>

<form action="{{ route('products.update', $product) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="text" name="name" value="{{ $product->name }}" required><br>
    <textarea name="category">{{ $product->category }}</textarea><br>
    <input type="number" step="0.01" name="price" value="{{ $product->price }}" required><br>
    <button type="submit">Оновити</button>
</form>