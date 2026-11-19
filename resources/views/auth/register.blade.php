@extends('layouts.app')
@section('title', 'Регистрация - Чудо Обувь')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card product-card">
                <div class="card-header">Регистрация</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="login" class="form-label">Логин</label>
                            <input id="login" type="text" name="login" value="{{ old('login') }}" class="form-control @error('login') is-invalid @enderror" required autofocus maxlength="100">
                            @error('login')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Фамилия</label>
                            <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" class="form-control @error('last_name') is-invalid @enderror" required maxlength="100">
                            @error('last_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="first_name" class="form-label">Имя</label>
                            <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" class="form-control @error('first_name') is-invalid @enderror" required maxlength="100">
                            @error('first_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="middle_name" class="form-label">Отчество (необязательно)</label>
                            <input id="middle_name" type="text" name="middle_name" value="{{ old('middle_name') }}" class="form-control @error('middle_name') is-invalid @enderror" maxlength="100">
                            @error('middle_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Пароль</label>
                            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                            @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            <small class="form-text text-muted">Минимум 8 символов.</small>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Повторите пароль</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
                        </div>
                        <button type="submit" class="btn btn-outline-primary w-100">Зарегистрироваться</button>
                    </form>
                    <div class="text-center mt-3">
                        Уже есть аккаунт? <a href="{{ route('login') }}">Войти</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
