@extends('layouts.app')
@section('title','Menu')
@section('header','Kelola Menu')
@section('content')
<div class="flex justify-between mb-4">
    <form class="flex gap-2">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari menu..." class="input max-w-xs">
        <select name="category_id" class="input max-w-xs">
            <option value="">Semua Kategori</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" {{ request('category_id')==$c->id ? 'selected' : '' }}>{{ $c->icon }} {{ $c->name }}</option>
            @endforeach
        </select>
        <button class="btn-secondary">Cari</button>
    </form>
    <a href="{{ route('admin.menu.create') }}" class="btn-primary" data-testid="add-menu-btn">+ Tambah Menu</a>
</div>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-coffee-50 dark:bg-coffee-700/50 text-left">
            <tr><th class="px-4 py-3">Foto</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        @forelse($menus as $m)
            <tr class="border-t border-coffee-100 dark:border-coffee-700">
                <td class="px-4 py-2">
                    @php $img = $m->primaryImage->path ?? null; @endphp
                    @if($img)<img src="{{ asset('storage/'.$img) }}" class="w-12 h-12 object-cover rounded-lg">@else <div class="w-12 h-12 rounded-lg bg-coffee-100 dark:bg-coffee-700 flex items-center justify-center">{{ $m->category->icon ?? '🍽' }}</div>@endif
                </td>
                <td class="font-semibold">{{ $m->name }}</td>
                <td>{{ $m->category->name ?? '-' }}</td>
                <td class="font-mono-num">Rp {{ number_format((float)$m->price,0,',','.') }}</td>
                <td class="font-mono-num">{{ $m->stock_qty }}</td>
                <td>
                    @if($m->is_available && $m->stock_qty > $m->stock_alert_min)<span class="badge-green">Aktif</span>
                    @elseif($m->stock_qty > 0)<span class="badge-amber">Kritis</span>
                    @else <span class="badge-red">Habis</span>@endif
                </td>
                <td class="space-x-2 whitespace-nowrap">
                    <a href="{{ route('admin.menu.edit', $m->id) }}" class="btn-ghost btn-sm">✏️ Edit</a>
                    <form method="POST" action="{{ route('admin.menu.destroy', $m->id) }}" class="inline" onsubmit="return confirm('Hapus menu ini?')">
                        @csrf @method('DELETE')
                        <button class="btn-danger btn-sm">🗑</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="py-10 text-center text-coffee-500">Belum ada menu. <a href="{{ route('admin.menu.create') }}" class="text-caramel-500 font-semibold">Tambah menu pertama</a></td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $menus->links() }}</div>
@endsection
