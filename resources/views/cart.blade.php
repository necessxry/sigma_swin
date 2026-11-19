@extends('layouts.app')
@section('title', 'Корзина')
@section('content')
<div class="container">
    <h1 class="catalog-title mb-4">Корзина</h1>
    @if ($items->isEmpty())
        <div class="alert alert-info">Корзина пуста.</div>
        <a href="{{ route('catalog') }}" class="btn btn-outline-primary">Перейти в каталог</a>
    @else
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead><tr><th>Товар</th><th>Размер</th><th>Цена</th><th>Количество</th><th>Сумма</th><th></th></tr></thead>
                <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->product->manufacturer->name ?? '' }} {{ $item->product->name }}</td>
                        <td>{{ $item->size->value }}</td>
                        <td>{{ number_format($item->catalogPrice, 2, ',', ' ') }} ₽</td>
                        <td>
                            <form action="{{ route('cart.update', $item) }}" method="POST" class="d-flex gap-2">
                                @csrf @method('PATCH')
                                <input type="number" name="quantity" min="1" max="{{ $item->available_quantity }}" value="{{ $item->cartQuantity }}" class="form-control" style="max-width:100px">
                                <button class="btn btn-sm btn-outline-primary" type="submit">OK</button>
                            </form>
                        </td>
                        <td>{{ number_format($item->lineTotal, 2, ',', ' ') }} ₽</td>
                        <td>
                            <form action="{{ route('cart.destroy', $item) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot><tr><th colspan="4" class="text-end">Итого:</th><th>{{ number_format($total, 2, ',', ' ') }} ₽</th><th></th></tr></tfoot>
            </table>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('cart.clear') }}" method="POST">@csrf @method('DELETE')<button class="btn btn-outline-danger">Очистить корзину</button></form>
            <form action="{{ route('cart.checkout') }}" method="POST">@csrf<button class="btn btn-outline-success">Оформить заказ</button></form>
        </div>
    @endif
</div>
@endsection
