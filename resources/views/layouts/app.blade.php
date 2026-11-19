<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Чудо Обувь')</title>
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <script defer src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</head>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('catalog') }}">
                <img class="brand-logo" src="{{ asset('assets/images/logo.png') }}" alt="Чудо Обувь">
                <span class="brand-name">Чудо Обувь</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavigation">
                @auth
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link" href="{{ route('catalog') }}">Каталог</a></li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('cart.index') }}">Корзина
                                @if (array_sum(session('cart', [])) > 0)
                                    <span class="badge discount-badge">{{ array_sum(session('cart', [])) }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Личный кабинет</a></li>
                    </ul>
                    <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">
                        <a class="nav-link" href="{{ route('home') }}"><span class="navbar-text fw-semibold">{{ auth()->user()->full_name }}</span></a>
                        <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit" class="btn btn-outline-secondary btn-sm">Выйти</button></form>
                    </div>
                @else
                    <ul class="navbar-nav ms-auto flex-row gap-3">
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Войти</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Регистрация</a></li>
                    </ul>
                @endauth
            </div>
        </div>
    </nav>
    <main class="py-4">
        <div class="container">
            @foreach (['success' => 'success', 'error' => 'danger', 'info' => 'info'] as $key => $type)
                @if (session($key))
                    <div class="alert alert-{{ $type }} alert-dismissible" role="alert">
                        {{ session($key) }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
                    </div>
                @endif
            @endforeach
        </div>
        @yield('content')
    </main>
    @stack('scripts')
</div>
</body>
</html>
