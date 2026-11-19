@extends('layouts.app')
@section('title', 'Новый заказ')
@section('content')
<div class="container">
    <h1 class="catalog-title mb-4">Формирование нового заказа</h1>
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card product-card"><div class="card-body">
                <h2 class="h5 mb-3">1. Каталог товаров</h2>
                <form method="GET" action="{{ route('home.orders.create') }}" class="row g-2 mb-3">
                    <div class="col-sm-9"><input type="search" name="search" value="{{ $search }}" maxlength="255" class="form-control" placeholder="Поиск по наименованию..."></div>
                    <div class="col-sm-3 d-grid"><button class="btn btn-outline-primary" type="submit">Найти</button></div>
                </form>
                @forelse ($products as $product)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div>{{ $product->manufacturer->name ?? '' }} {{ $product->name }}<div class="text-muted small">{{ number_format($product->price, 2, ',', ' ') }} ₽</div></div>
                        <a href="{{ route('home.orders.select', $product) }}" class="btn btn-sm btn-outline-primary">Выбрать</a>
                    </div>
                @empty
                    <div class="alert alert-info">Товары не найдены.</div>
                @endforelse
            </div></div>
        </div>
        <div class="col-lg-5">
            <div class="card product-card mb-4"><div class="card-body">
                <h2 class="h5 mb-3">2. Черновик заказа</h2>
                @if ($draftItems->isEmpty())
                    <div class="alert alert-info">Черновик пуст.</div>
                @else
                    <div class="table-responsive"><table class="table table-sm">
                        <thead><tr><th>Товар</th><th>Размер</th><th>Кол-во</th><th>Сумма</th><th></th></tr></thead>
                        <tbody>
                        @foreach ($draftItems as $item)
                            <tr>
                                <td>{{ $item->product->manufacturer->name ?? '' }} {{ $item->product->name }}</td>
                                <td>{{ $item->size->value }}</td>
                                <td>{{ $item->draftQuantity }}</td>
                                <td>{{ number_format($item->lineTotal, 2, ',', ' ') }} ₽</td>
                                <td><form action="{{ route('home.orders.draft.remove', $item) }}" method="POST">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">X</button></form></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table></div>
                    <div class="text-end fw-bold">Итого: {{ number_format($draftTotal, 2, ',', ' ') }} ₽</div>
                @endif
            </div></div>
            <div class="card product-card"><div class="card-body">
                <h2 class="h5 mb-3">3. Данные заказа</h2>
                <form method="POST" action="{{ route('home.orders.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Клиент</label>
                        <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                            <option value="">Выберите клиента</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->user_id }}" @selected(old('user_id') == $client->user_id)>{{ $client->full_name }}</option>
                            @endforeach
                        </select>
                        @error('user_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="order_date" class="form-label">Дата заказа</label>
                        <input type="date" id="order_date" name="order_date" value="{{ old('order_date', now()->toDateString()) }}" class="form-control @error('order_date') is-invalid @enderror" required>
                        @error('order_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <button type="submit" class="btn btn-outline-success w-100" @disabled($draftItems->isEmpty())>Создать заказ</button>
                </form>
            </div></div>
        </div>
    </div>
</div>
@endsection
