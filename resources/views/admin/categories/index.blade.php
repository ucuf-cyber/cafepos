@extends('layouts.app')
@section('title','Kategori')
@section('header','Kategori Menu')
@section('content')
<div class="flex justify-end mb-4"><a href="{{ route('admin.categories.create') }}" class="btn-primary" data-testid="add-cat">+ Tambah Kategori</a></div>
<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-coffee-50 dark:bg-coffee-700/50 text-left"><tr><th class="px-4 py-3">Icon</th><th>Nama</th><th>Urutan</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse($categories as $c)
        <tr class="border-t border-coffee-100 dark:border-coffee-700">
            <td class="px-4 py-2 text-2xl">{{ $c->icon }}</td>
            <td class="font-semibold">{{ $c->name }}</td>
            <td>{{ $c->sort_order }}</td>
            <td>@if($c->is_active)<span class="badge-green">Aktif</span>@else<span class="badge-gray">Nonaktif</span>@endif</td>
            <td class="space-x-2">
                <a href="{{ route('admin.categories.edit', $c->id) }}" class="btn-ghost btn-sm">✏️</a>
                <form method="POST" action="{{ route('admin.categories.destroy', $c->id) }}" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn-danger btn-sm">🗑</button></form>
            </td>
        </tr>
        @empty <tr><td colspan="5" class="py-6 text-center text-coffee-500">Belum ada kategori</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
