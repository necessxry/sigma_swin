@extends('layouts.app')
@section('title', $product->name)
@section('content')
<div class="container">
    <div class="mb-3"><a href="{{ route('catalog') }}" class="btn btn-outline-secondary">← Назад в каталог</a></div>
    <div class="card product-card">
        <div class="card-body p-4">
            <h1 class="catalog-title">{{ $product->manufacturer->name ?? '' }} {{ $product->name }}</h1>
            <dl class="row mt-3">
                <dt class="col-sm-3">Категория</dt><dd class="col-sm-9">{{ $product->category->name ?? 'Не указана' }}</dd>
                <dt class="col-sm-3">Описание</dt><dd class="col-sm-9">{{ $product->description }}</dd>
                <dt class="col-sm-3">Состав</dt><dd class="col-sm-9">{{ $product->composition }}</dd>
                <dt class="col-sm-3">Цена</dt><dd class="col-sm-9">{{ number_format($product->catalogPrice, 2, ',', ' ') }} ₽</dd>
            </dl>
            @auth
                @php($available = $product->stockItems->where('available_quantity', '>', 0)->sortBy(fn($item) => $item->size->value))
                @if ($available->isEmpty())
                    <div class="alert alert-info">Товара нет в наличии.</div>
                @else
                    <form method="POST" action="{{ route('cart.store') }}" class="row g-3 align-items-end">
                        @csrf
                        <div class="col-sm-6">
                            <label for="stock_item_id" class="form-label">Размер</label>
                            <select id="stock_item_id" name="stock_item_id" class="form-select @error('stock_item_id') is-invalid @enderror" required>
                                @foreach ($available as $item)
                                    <option value="{{ $item->stock_item_id }}" @selected(old('stock_item_id') == $item->stock_item_id)>
                                        {{ $item->size->value }} (доступно: {{ $item->available_quantity }})
                                    </option>
                                @endforeach
                            </select>
                            @error('stock_item_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-sm-3">
                            <label for="quantity" class="form-label">Количество</label>
                            <input type="number" id="quantity" name="quantity" min="1" value="{{ old('quantity', 1) }}" class="form-control @error('quantity') is-invalid @enderror" required>
                            @error('quantity')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-sm-3 d-grid"><button type="submit" class="btn btn-outline-primary">Добавить в корзину</button></div>
                    </form>
                @endif
            @else
                <div class="alert alert-info">Для добавления товара в корзину войдите в систему.</div>
            @endauth
        </div>
    </div>
</div>
@endsection
