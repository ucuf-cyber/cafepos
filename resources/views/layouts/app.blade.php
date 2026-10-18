<!DOCTYPE html>
<html lang="id" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'CaféPos'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen">
<div x-data="{ open: false }" class="flex min-h-screen">
    <aside class="hidden md:flex flex-col w-64 bg-coffee-900 text-cream-50">
        <div class="px-6 py-6 border-b border-coffee-700">
            <div class="font-display text-2xl">☕ CaféPos</div>
            <div class="text-xs text-cream-100/60 mt-1">{{ ucfirst(auth()->user()->role ?? '') }} Panel</div>
        </div>
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            @include('layouts._sidebar')
        </nav>
        <div class="p-4 border-t border-coffee-700">
            <div class="text-sm font-semibold">{{ auth()->user()->name ?? '' }}</div>
            <div class="text-xs text-cream-100/60 mb-3">{{ auth()->user()->email ?? '' }}</div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="w-full text-left text-sm text-cream-100 hover:text-caramel-400 transition" data-testid="logout-btn">→ Keluar</button>
            </form>
        </div>
    </aside>
    <!-- Mobile menu -->
    <div class="md:hidden fixed inset-0 z-40 transition" :class="open ? 'pointer-events-auto' : 'pointer-events-none'">
        <div class="absolute inset-0 bg-black transition-opacity" :class="open ? 'opacity-50' : 'opacity-0'" @click="open=false"></div>
        <aside class="absolute left-0 top-0 h-full w-72 bg-coffee-900 text-cream-50 transform transition-transform" :class="open ? 'translate-x-0' : '-translate-x-full'">
            <div class="px-6 py-6 border-b border-coffee-700 flex justify-between items-center">
                <div class="font-display text-2xl">☕ CaféPos</div>
                <button @click="open=false" class="text-cream-50">✕</button>
            </div>
            <nav class="flex-1 px-3 py-4 space-y-1">@include('layouts._sidebar')</nav>
            <div class="p-4 border-t border-coffee-700">
                <form action="{{ route('logout') }}" method="POST">@csrf<button class="text-sm">→ Keluar</button></form>
            </div>
        </aside>
    </div>

    <main class="flex-1 flex flex-col min-w-0">
        <header class="sticky top-0 z-30 backdrop-blur bg-white/85 dark:bg-coffee-900/85 border-b border-coffee-100 dark:border-coffee-700">
            <div class="flex items-center justify-between px-4 md:px-8 py-4">
                <div class="flex items-center gap-3">
                    <button class="md:hidden p-2 rounded-lg hover:bg-coffee-100 dark:hover:bg-coffee-700" @click="open = true" data-testid="menu-toggle">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="font-display text-xl md:text-2xl font-semibold text-coffee-900 dark:text-cream-50">@yield('header', 'Dashboard')</h1>
                        <div class="text-xs text-coffee-500 dark:text-cream-100/60">@yield('subheader', '')</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="toggleDarkMode()" class="p-2 rounded-lg hover:bg-coffee-100 dark:hover:bg-coffee-700 transition" data-testid="dark-toggle" title="Ganti tema">
                        <svg class="w-5 h-5 hidden dark:block" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3v2m0 14v2M5.6 5.6l1.4 1.4m10 10l1.4 1.4M3 12h2m14 0h2M5.6 18.4l1.4-1.4m10-10l1.4-1.4M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
                        <svg class="w-5 h-5 dark:hidden" fill="currentColor" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                    </button>
                </div>
            </div>
        </header>
        @if(session('success'))
            <div class="mx-4 md:mx-8 mt-4 p-3 rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-200" data-testid="flash-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mx-4 md:mx-8 mt-4 p-3 rounded-xl bg-red-100 text-red-800 border border-red-200" data-testid="flash-error">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="mx-4 md:mx-8 mt-4 p-3 rounded-xl bg-red-100 text-red-800 border border-red-200">
                @foreach($errors->all() as $err) <div>• {{ $err }}</div> @endforeach
            </div>
        @endif
        <div class="flex-1 px-4 md:px-8 py-6 page-enter">
            @yield('content')
        </div>
    </main>
</div>
@stack('scripts')
</body>
</html>
