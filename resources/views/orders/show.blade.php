@extends('layouts.app')
@section('title', 'Заказ №' . $order->order_id)
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="catalog-title mb-0">Заказ №{{ $order->order_id }}</h1>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">← К списку заказов</a>
    </div>
    <div class="card product-card mb-4"><div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Клиент</dt><dd class="col-sm-9">{{ $order->user?->full_name ?? 'Клиент не найден' }}</dd>
            <dt class="col-sm-3">Дата заказа</dt><dd class="col-sm-9">{{ $order->order_date?->format('d.m.Y') }}</dd>
            <dt class="col-sm-3">Итоговая сумма</dt><dd class="col-sm-9 fw-bold">{{ number_format($order->totalSum, 2, ',', ' ') }} ₽</dd>
        </dl>
    </div></div>
    <h2 class="h5 mb-3">Товарные позиции</h2>
    @if ($order->items->isEmpty())
        <div class="alert alert-info">В заказе нет товарных позиций.</div>
    @else
        <div class="table-responsive"><table class="table table-bordered align-middle">
            <thead><tr><th>Товар</th><th>Размер</th><th class="text-center">Количество</th><th class="text-end">Цена за единицу</th><th class="text-end">Сумма</th></tr></thead>
            <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->stockItem->product->manufacturer->name ?? '' }} {{ $item->stockItem->product->name ?? '' }}</td>
                    <td>{{ $item->stockItem->size->value ?? '' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end">{{ number_format($item->unit_price, 2, ',', ' ') }} ₽</td>
                    <td class="text-end">{{ number_format($item->quantity * $item->unit_price, 2, ',', ' ') }} ₽</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot><tr><th colspan="4" class="text-end">Итого:</th><th class="text-end">{{ number_format($order->totalSum, 2, ',', ' ') }} ₽</th></tr></tfoot>
        </table></div>
    @endif
</div>
@endsection
