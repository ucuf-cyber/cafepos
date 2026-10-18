@extends('layouts.app')
@section('title','Admin Dashboard')
@section('header','Admin Dashboard')
@section('subheader','Overview operasional kafe Anda')
@section('content')
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="card p-5">
        <div class="text-xs uppercase tracking-wider text-coffee-500">Pesanan Hari Ini</div>
        <div class="font-mono-num text-3xl font-bold mt-1 text-coffee-900 dark:text-cream-50">{{ $stats['orders_today'] }}</div>
    </div>
    <div class="card p-5">
        <div class="text-xs uppercase tracking-wider text-coffee-500">Revenue Hari Ini</div>
        <div class="font-mono-num text-2xl font-bold mt-1 text-caramel-600">Rp {{ number_format($stats['revenue_today'],0,',','.') }}</div>
    </div>
    <div class="card p-5">
        <div class="text-xs uppercase tracking-wider text-coffee-500">Menu Aktif</div>
        <div class="font-mono-num text-3xl font-bold mt-1">{{ $stats['menu_active'] }}</div>
    </div>
    <div class="card p-5">
        <div class="text-xs uppercase tracking-wider text-coffee-500">Stok Kritis</div>
        <div class="font-mono-num text-3xl font-bold mt-1 {{ $stats['critical_stock'] > 0 ? 'text-red-500' : '' }}">{{ $stats['critical_stock'] }}</div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-4 mt-6">
    <div class="card p-5">
        <div class="font-display text-lg font-semibold mb-3">📋 Aktivitas Terkini</div>
        <div class="space-y-2 text-sm">
            @forelse($recent as $o)
            <div class="flex items-center justify-between py-2 border-b border-coffee-100 dark:border-coffee-700 last:border-0">
                <div>
                    <span class="font-semibold">{{ $o->order_number }}</span>
                    · Meja {{ $o->table->table_number }} · {{ ucfirst($o->status_label) }}
                </div>
                <span class="font-mono-num text-coffee-500 text-xs">{{ $o->created_at->diffForHumans() }}</span>
            </div>
            @empty
            <div class="text-coffee-500">Belum ada aktivitas</div>
            @endforelse
        </div>
    </div>
    <div class="card p-5">
        <div class="font-display text-lg font-semibold mb-3">⚠️ Stok Perlu Perhatian</div>
        @forelse($lowStock as $m)
        <div class="flex items-center justify-between py-2 border-b border-coffee-100 dark:border-coffee-700 last:border-0">
            <div>
                <div class="font-semibold text-sm">{{ $m->name }}</div>
                <div class="text-xs text-coffee-500">Min: {{ $m->stock_alert_min }}</div>
            </div>
            <div class="font-mono-num font-bold {{ $m->stock_qty <= 0 ? 'text-red-500' : 'text-amber-500' }}">{{ $m->stock_qty }}</div>
        </div>
        @empty
        <div class="text-coffee-500 text-sm">Semua stok aman ✓</div>
        @endforelse
    </div>
</div>
@endsection
