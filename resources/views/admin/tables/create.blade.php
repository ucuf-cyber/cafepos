@extends('layouts.app')
@section('title', isset($table) ? 'Edit Meja' : 'Tambah Meja')
@section('header', isset($table) ? 'Edit Meja' : 'Tambah Meja')
@section('content')
<form method="POST" action="{{ isset($table) ? route('admin.tables.update', $table->id) : route('admin.tables.store') }}" class="card p-6 space-y-4 max-w-xl">
    @csrf @if(isset($table)) @method('PUT') @endif
    <div><label class="label">Nomor Meja *</label><input name="table_number" value="{{ old('table_number', $table->table_number ?? '') }}" class="input" required></div>
    <div><label class="label">Kapasitas *</label><input type="number" name="capacity" value="{{ old('capacity', $table->capacity ?? 2) }}" class="input" min="1" required></div>
    <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $table->is_active ?? true) ? 'checked' : '' }}> Aktif</label>
    <div class="flex gap-2"><button class="btn-primary">💾 Simpan</button><a href="{{ route('admin.tables.index') }}" class="btn-secondary">Batal</a></div>
</form>
@endsection
