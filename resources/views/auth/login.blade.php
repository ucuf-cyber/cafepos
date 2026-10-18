@extends('layouts.customer')
@section('title', 'Login — CaféPos')
@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12 bg-gradient-to-br from-cream-50 to-coffee-100 dark:from-coffee-900 dark:to-coffee-800">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="text-5xl mb-3">☕</div>
            <h1 class="font-display text-4xl font-semibold text-coffee-900 dark:text-cream-50">CaféPos</h1>
            <p class="text-coffee-600 dark:text-cream-100/60 mt-1">Sistem Kasir & Manajemen Kafe</p>
        </div>
        <div class="card p-8">
            <h2 class="font-display text-2xl font-semibold text-coffee-900 dark:text-cream-50 mb-1">Masuk</h2>
            <p class="text-sm text-coffee-500 dark:text-cream-100/60 mb-6">Owner · Admin · Kasir</p>
            @if($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-red-100 text-red-800 text-sm">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="input" required autofocus data-testid="login-email">
                </div>
                <div>
                    <label class="label">Password</label>
                    <input type="password" name="password" class="input" required data-testid="login-password">
                </div>
                <label class="flex items-center gap-2 text-sm text-coffee-600 dark:text-cream-100/80">
                    <input type="checkbox" name="remember" class="rounded"> Ingat saya
                </label>
                <button type="submit" class="btn-primary w-full" data-testid="login-submit">Masuk</button>
            </form>
            <div class="mt-6 p-4 rounded-xl bg-coffee-50 dark:bg-coffee-700/30 text-xs text-coffee-600 dark:text-cream-100/70 space-y-1">
                <div class="font-semibold text-coffee-800 dark:text-cream-50 mb-1">Akun Demo:</div>
                <div>👑 owner@cafepos.com · password</div>
                <div>🛠 admin@cafepos.com · password</div>
                <div>🎯 kasir@cafepos.com · password</div>
            </div>
        </div>
        <p class="text-center text-xs text-coffee-500 dark:text-cream-100/40 mt-6">© {{ date('Y') }} CaféPos · Powered by Laravel 11</p>
    </div>
</div>
@endsection
