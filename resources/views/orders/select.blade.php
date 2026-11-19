@extends('layouts.app')
@section('title', $product->name . ' — Новый заказ')
@section('content')
<div class="container">
    <div class="mb-3"><a href="{{ route('home.orders.create') }}" class="btn btn-outline-secondary">← Назад к формированию заказа</a></div>
    <div class="card product-card"><div class="card-body p-4">
        <h1 class="h3 mb-3">{{ $product->name }}</h1>
        <dl class="row">
            <dt class="col-sm-3">Производитель</dt><dd class="col-sm-9">{{ $product->manufacturer->name ?? 'Не указан' }}</dd>
            <dt class="col-sm-3">Категория</dt><dd class="col-sm-9">{{ $product->category->name ?? 'Не указана' }}</dd>
            <dt class="col-sm-3">Описание</dt><dd class="col-sm-9">{{ $product->description }}</dd>
            <dt class="col-sm-3">Состав</dt><dd class="col-sm-9">{{ $product->composition }}</dd>
            <dt class="col-sm-3">Цена за единицу</dt><dd class="col-sm-9">{{ number_format($product->price, 2, ',', ' ') }} ₽</dd>
        </dl>
        @php($available = $product->stockItems->where('available_quantity', '>', 0)->sortBy(fn($item) => $item->size->value))
        @if ($available->isEmpty())
            <div class="alert alert-info">Нет доступных размеров.</div>
        @else
            <form method="POST" action="{{ route('home.orders.draft.add') }}" class="row g-3 align-items-end">
                @csrf
                <div class="col-sm-6">
                    <label for="stock_item_id" class="form-label">Размер</label>
                    <select id="stock_item_id" name="stock_item_id" class="form-select @error('stock_item_id') is-invalid @enderror" required>
                        @foreach ($available as $stockItem)
                            <option value="{{ $stockItem->stock_item_id }}" @selected(old('stock_item_id') == $stockItem->stock_item_id)>{{ $stockItem->size->value }} (доступно: {{ $stockItem->available_quantity }})</option>
                        @endforeach
                    </select>
                    @error('stock_item_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="col-sm-3">
                    <label for="quantity" class="form-label">Кол-во пар</label>
                    <input type="number" id="quantity" name="quantity" min="1" value="{{ old('quantity', 1) }}" class="form-control @error('quantity') is-invalid @enderror" required>
                    @error('quantity')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="col-sm-3 d-grid"><button type="submit" class="btn btn-outline-primary">Добавить</button></div>
            </form>
        @endif
    </div></div>
</div>
@endsection
