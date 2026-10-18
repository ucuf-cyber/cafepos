@extends('layouts.app')
@section('title', isset($category) ? 'Edit Kategori' : 'Tambah Kategori')
@section('header', isset($category) ? 'Edit Kategori' : 'Tambah Kategori')
@section('content')
<form method="POST" action="{{ isset($category) ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}" class="card p-6 space-y-4 max-w-xl">
    @csrf @if(isset($category)) @method('PUT') @endif
    <div><label class="label">Nama *</label><input name="name" value="{{ old('name', $category->name ?? '') }}" class="input" required></div>
    <div><label class="label">Icon (emoji)</label><input name="icon" value="{{ old('icon', $category->icon ?? '') }}" class="input" placeholder="☕"></div>
    <div><label class="label">Urutan</label><input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="input"></div>
    <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}> Aktif</label>
    <div class="flex gap-2"><button class="btn-primary">💾 Simpan</button><a href="{{ route('admin.categories.index') }}" class="btn-secondary">Batal</a></div>
</form>
@endsection
