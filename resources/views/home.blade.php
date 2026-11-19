@extends('layouts.app')
@section('title', 'Личный кабинет')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="catalog-title mb-0">{{ auth()->user()->isStaff() ? 'Управление заказами' : 'Мои заказы' }}</h1>
        @if (auth()->user()->isStaff())
            <a href="{{ route('home.orders.create') }}" class="btn btn-outline-primary">Сформировать заказ</a>
        @endif
    </div>

    @if (auth()->user()->isStaff())
        @if ($orders->isEmpty())
            <div class="alert alert-info">Заказов пока нет.</div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead><tr><th>№ заказа</th><th>Дата</th><th>Клиент</th><th>Количество позиций</th><th>Действия</th></tr></thead>
                    <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>{{ $order->order_id }}</td>
                            <td>{{ $order->order_date?->format('d.m.Y') }}</td>
                            <td>{{ $order->user?->full_name ?? 'Клиент не найден' }}</td>
                            <td>{{ $order->items_count }}</td>
                            <td class="d-flex gap-2">
                                <a href="{{ route('home.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">Просмотр</a>
                                <a href="{{ route('home.orders.edit', $order) }}" class="btn btn-sm btn-outline-secondary">Изменить</a>
                                <form action="{{ route('home.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Удалить заказ №{{ $order->order_id }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @else
        @if ($orders->isEmpty())
            <div class="alert alert-info">У вас пока нет заказов.</div>
        @else
            @foreach ($orders as $order)
                <div class="card product-card mb-3">
                    <div class="card-body">
                        <h2 class="h5">Заказ №{{ $order->order_id }} от {{ $order->order_date?->format('d.m.Y') }}</h2>
                        @if ($order->items->isEmpty())
                            <p class="text-muted">В заказе нет товарных позиций.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead><tr><th>Товар</th><th>Размер</th><th>Количество</th><th>Цена</th></tr></thead>
                                    <tbody>
                                    @foreach ($order->items as $item)
                                        <tr>
                                            <td>{{ $item->stockItem->product->manufacturer->name ?? '' }} {{ $item->stockItem->product->name ?? '' }}</td>
                                            <td>{{ $item->stockItem->size->value ?? '' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ number_format($item->unit_price, 2, ',', ' ') }} ₽</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                        <div class="text-end fw-bold">Итого: {{ number_format($order->totalSum, 2, ',', ' ') }} ₽</div>
                    </div>
                </div>
            @endforeach
        @endif
    @endif
</div>
@endsection
