@extends('layouts.app')
@section('title','Stok')
@section('header','Kelola Stok Menu')
@section('content')
<div class="grid lg:grid-cols-3 gap-4">
<div class="card p-5 lg:col-span-2 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="text-left text-coffee-500"><tr><th class="py-2">Menu</th><th>Stok</th><th>Alert</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach($menus as $m)
        <tr class="border-t border-coffee-100 dark:border-coffee-700">
            <td class="py-2 font-semibold">{{ $m->name }}</td>
            <td class="font-mono-num">{{ $m->stock_qty }}</td>
            <td class="font-mono-num text-coffee-500">{{ $m->stock_alert_min }}</td>
            <td>
                @if($m->stock_qty<=0)<span class="badge-red">Habis</span>
                @elseif($m->stock_qty <= $m->stock_alert_min)<span class="badge-amber">Kritis</span>
                @else <span class="badge-green">Aman</span>@endif
            </td>
            <td>
                <form method="POST" action="{{ route('admin.stock.update', $m->id) }}" class="flex gap-1">
                    @csrf @method('PATCH')
                    <input type="number" name="qty_change" class="input !py-1 !px-2 w-20 font-mono-num" placeholder="±qty" required>
                    <select name="reason" class="input !py-1 !px-2 w-28"><option value="restock">Restock</option><option value="koreksi">Koreksi</option></select>
                    <button class="btn-primary btn-sm">Simpan</button>
                </form>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="card p-5">
    <div class="font-display text-lg font-semibold mb-3">📜 Riwayat Stok</div>
    <div class="space-y-2 text-xs max-h-96 overflow-y-auto">
        @forelse($history as $h)
        <div class="py-1.5 border-b border-coffee-100 dark:border-coffee-700 last:border-0">
            <div class="font-semibold">{{ $h->menu_name }}</div>
            <div class="text-coffee-500">{{ $h->qty_before }} → {{ $h->qty_after }} ({{ $h->qty_change>0?'+':'' }}{{ $h->qty_change }}) · {{ ucfirst($h->reason) }}</div>
            <div class="text-coffee-400">{{ $h->created_at?->diffForHumans() }}</div>
        </div>
        @empty <div class="text-coffee-500">Belum ada perubahan</div>
        @endforelse
    </div>
</div>
</div>
@endsection
