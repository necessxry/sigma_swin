@extends('layouts.app')
@section('title', 'Каталог обуви - Чудо Обувь')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h1 class="catalog-title mb-1">Каталог обуви</h1>
            <p class="text-muted mb-0">Качественная обувь для всей семьи</p>
        </div>
        <small class="text-muted">Расчёт цен на {{ $calculationDate->format('d.m.Y') }}</small>
    </div>

    <form method="GET" action="{{ route('catalog') }}" class="row g-2 mb-4">
        <div class="col-md-5">
            <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Поиск по названию или описанию">
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">Все категории</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->category_id }}" @selected(request('category') == $category->category_id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="sort" class="form-select">
                <option value="">Без сортировки</option>
                <option value="price_asc" @selected(request('sort') === 'price_asc')>Сначала дешевле</option>
                <option value="price_desc" @selected(request('sort') === 'price_desc')>Сначала дороже</option>
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button class="btn btn-outline-primary" type="submit">Применить</button>
        </div>
    </form>

    @forelse ($products as $product)
        @php
            $imagePath = 'assets/images/catalog/' . ($product->image_path ?? '');
            $imageExists = !empty($product->image_path) && file_exists(public_path($imagePath));
            $imageUrl = $imageExists ? asset($imagePath) : asset('assets/images/picture.png');
            $isLowStock = $product->totalAvailableQuantity <= 3;
        @endphp
        <article class="product-card mb-3 {{ $isLowStock ? 'product-card-low' : '' }}">
            <div class="row g-0">
                <div class="col-md-3">
                    <img src="{{ $imageUrl }}" class="product-image" alt="{{ $product->name }}">
                </div>
                <div class="col-md-9">
                    <div class="product-content">
                        <h2 class="product-heading">{{ $product->manufacturer->name ?? 'Производитель не указан' }} | {{ $product->name }}</h2>
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <div><span class="product-label">Категория:</span> {{ $product->category->name ?? 'Не указана' }}</div>
                                <div><span class="product-label">Количество:</span> {{ $product->totalAvailableQuantity > 5 ? 'много' : 'мало' }}</div>
                                <div><span class="product-label">Состав:</span> {{ $product->composition ?? 'Не указан' }}</div>
                            </div>
                            <div class="col-md-5 text-md-end">
                                @if (!$product->hadPreviousMonthOrder)
                                    <div class="old-price">{{ number_format($product->price, 2, ',', ' ') }} ₽</div>
                                    <span class="discount-badge">-25%</span>
                                @endif
                                <div class="product-price">{{ number_format($product->catalogPrice, 2, ',', ' ') }} ₽</div>
                                <a href="{{ route('catalog.show', $product) }}" class="btn btn-outline-primary mt-2">Подробнее</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    @empty
        <div class="alert alert-info">Товары не найдены.</div>
    @endforelse
</div>
@endsection
