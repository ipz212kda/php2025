<h1>Створити товар</h1>

<form action="{{ route('products.store') }}" method="POST">
    @csrf
    <input type="text" name="name" placeholder="Назва" required><br>
    <input type="text" name="category" placeholder="Категорія" required><br>
    <input type="number" step="0.01" name="price" placeholder="Ціна" required><br>
    <button type="submit">Зберегти</button>
</form>