@extends('layouts.app')
@section('title','Monitor Stok')
@section('header','Monitor Stok Menu')
@section('subheader','Tampilan view-only — Admin yang mengelola stok')
@section('content')
<div class="card overflow-hidden">
<table class="w-full text-sm">
    <thead class="bg-coffee-50 dark:bg-coffee-700/50 text-left"><tr><th class="px-4 py-3">Menu</th><th>Kategori</th><th>Stok</th><th>Alert</th><th>Status</th></tr></thead>
    <tbody>
    @foreach($menus as $m)
    <tr class="border-t border-coffee-100 dark:border-coffee-700">
        <td class="px-4 py-2 font-semibold">{{ $m->name }}</td>
        <td>{{ $m->category->name ?? '-' }}</td>
        <td class="font-mono-num font-bold">{{ $m->stock_qty }}</td>
        <td class="font-mono-num text-coffee-500">{{ $m->stock_alert_min }}</td>
        <td>
            @if($m->stock_qty<=0)<span class="badge-red">Habis</span>
            @elseif($m->stock_qty <= $m->stock_alert_min)<span class="badge-amber">Kritis</span>
            @elseif($m->stock_qty <= $m->stock_alert_min*2)<span class="badge-amber">Menipis</span>
            @else <span class="badge-green">Aman</span>@endif
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
</div>
@endsection
