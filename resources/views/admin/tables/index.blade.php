@extends('layouts.app')
@section('title','Meja & QR')
@section('header','Manajemen Meja & QR Code')
@section('content')
<div class="flex justify-end mb-4"><a href="{{ route('admin.tables.create') }}" class="btn-primary" data-testid="add-table">+ Tambah Meja</a></div>
<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
@forelse($tables as $t)
<div class="card p-5">
    <div class="flex justify-between items-start">
        <div>
            <div class="text-xs uppercase tracking-wider text-coffee-500">Meja</div>
            <div class="font-display text-3xl font-bold">{{ $t->table_number }}</div>
            <div class="text-sm text-coffee-500">Kapasitas {{ $t->capacity }} orang</div>
        </div>
        @if($t->is_active)<span class="badge-green">Aktif</span>@else<span class="badge-gray">Nonaktif</span>@endif
    </div>
    <div class="text-xs text-coffee-500 mt-3 break-all">{{ $t->qr_url }}</div>
    <div class="flex flex-wrap gap-2 mt-4">
        <a href="{{ $t->qr_url }}" target="_blank" class="btn-ghost btn-sm">👁 Preview</a>
        <a href="{{ route('admin.tables.qr', $t->id) }}" class="btn-secondary btn-sm">⬇️ Download QR</a>
        <a href="{{ route('admin.tables.print', $t->id) }}" target="_blank" class="btn-ghost btn-sm">🖨 Print</a>
        <a href="{{ route('admin.tables.edit', $t->id) }}" class="btn-ghost btn-sm">✏️</a>
        <form method="POST" action="{{ route('admin.tables.destroy', $t->id) }}" class="inline" onsubmit="return confirm('Hapus meja?')">@csrf @method('DELETE')<button class="btn-danger btn-sm">🗑</button></form>
    </div>
</div>
@empty <div class="col-span-full text-center text-coffee-500 py-10">Belum ada meja</div>
@endforelse
</div>
@endsection
