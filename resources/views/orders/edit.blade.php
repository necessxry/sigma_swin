@extends('layouts.app')
@section('title', 'Редактирование заказа №' . $order->order_id)
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="catalog-title mb-0">Редактирование заказа №{{ $order->order_id }}</h1>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">← К списку заказов</a>
    </div>
    <div class="card product-card mb-4"><div class="card-body">
        <div class="mb-3"><strong>Клиент:</strong> {{ $order->user?->full_name ?? 'Клиент не найден' }}</div>
        <form method="POST" action="{{ route('home.orders.update', $order) }}" class="row g-3 align-items-end">
            @csrf @method('PATCH')
            <div class="col-sm-6">
                <label for="order_date" class="form-label">Дата заказа</label>
                <input type="date" id="order_date" name="order_date" value="{{ old('order_date', $order->order_date?->toDateString()) }}" class="form-control @error('order_date') is-invalid @enderror" required>
                @error('order_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="col-sm-6"><button type="submit" class="btn btn-outline-primary">Сохранить дату</button></div>
        </form>
    </div></div>
    <h2 class="h5 mb-3">Товарные позиции</h2>
    @if ($order->items->isEmpty())
        <div class="alert alert-info">В заказе не осталось товарных позиций.</div>
    @else
        <div class="table-responsive"><table class="table table-bordered align-middle">
            <thead><tr><th>Товарная позиция</th><th>Размер</th><th class="text-center">Количество</th><th class="text-end">Цена за единицу</th><th class="text-end">Сумма</th><th class="text-end">Действие</th></tr></thead>
            <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->stockItem->product->manufacturer->name ?? '' }} {{ $item->stockItem->product->name ?? '' }}</td>
                    <td>{{ $item->stockItem->size->value ?? '' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end">{{ number_format($item->unit_price, 2, ',', ' ') }} ₽</td>
                    <td class="text-end">{{ number_format($item->quantity * $item->unit_price, 2, ',', ' ') }} ₽</td>
                    <td class="text-end">
                        <form action="{{ route('home.orders.items.destroy', [$order, $item]) }}" method="POST" onsubmit="return confirm('Удалить товарную позицию?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot><tr><th colspan="4" class="text-end">Итого:</th><th class="text-end">{{ number_format($order->totalSum, 2, ',', ' ') }} ₽</th><th></th></tr></tfoot>
        </table></div>
    @endif
</div>
@endsection
