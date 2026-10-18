@extends('layouts.app')
@section('title', isset($menu) ? 'Edit Menu' : 'Tambah Menu')
@section('header', isset($menu) ? 'Edit Menu' : 'Tambah Menu')
@section('content')
<form method="POST" action="{{ isset($menu) ? route('admin.menu.update', $menu->id) : route('admin.menu.store') }}" enctype="multipart/form-data" class="space-y-5 max-w-3xl">
    @csrf
    @if(isset($menu)) @method('PUT') @endif

    <div class="card p-6 space-y-4">
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="label">Nama Menu *</label>
                <input name="name" value="{{ old('name', $menu->name ?? '') }}" class="input" required data-testid="menu-name">
            </div>
            <div>
                <label class="label">Kategori *</label>
                <select name="category_id" class="input" required>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ ($menu->category_id ?? null)==$c->id ? 'selected' : '' }}>{{ $c->icon }} {{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Harga (Rp) *</label>
                <input name="price" type="number" step="0.01" value="{{ old('price', $menu->price ?? 0) }}" class="input font-mono-num" required>
            </div>
            <div>
                <label class="label">Urutan Tampil</label>
                <input name="sort_order" type="number" value="{{ old('sort_order', $menu->sort_order ?? 0) }}" class="input">
            </div>
        </div>
        <div>
            <label class="label">Deskripsi</label>
            <textarea name="description" rows="2" class="input">{{ old('description', $menu->description ?? '') }}</textarea>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="label">Stok Tersedia</label>
                <input name="stock_qty" type="number" value="{{ old('stock_qty', $menu->stock_qty ?? 0) }}" class="input font-mono-num">
            </div>
            <div>
                <label class="label">Alert jika ≤</label>
                <input name="stock_alert_min" type="number" value="{{ old('stock_alert_min', $menu->stock_alert_min ?? 10) }}" class="input font-mono-num">
            </div>
        </div>
        <div>
            <label class="label">Label</label>
            <div class="flex gap-3 text-sm">
                @php $labels = old('labels', $menu->labels ?? []); @endphp
                <label class="flex items-center gap-2"><input type="checkbox" name="labels[]" value="baru" {{ in_array('baru',(array)$labels)?'checked':'' }}> 🆕 Baru</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="labels[]" value="favorit" {{ in_array('favorit',(array)$labels)?'checked':'' }}> ⭐ Favorit</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="labels[]" value="pedas" {{ in_array('pedas',(array)$labels)?'checked':'' }}> 🌶 Pedas</label>
            </div>
        </div>
        <label class="flex items-center gap-2">
            <input type="checkbox" name="is_available" value="1" {{ old('is_available', $menu->is_available ?? true) ? 'checked' : '' }}>
            <span class="text-sm">Tersedia untuk dipesan</span>
        </label>
        <div>
            <label class="label">Upload Foto (maks 5, .jpg/.png)</label>
            <input type="file" name="images[]" multiple accept="image/*" class="input">
        </div>
        @if(isset($menu) && $menu->images->count())
        <div>
            <div class="text-sm font-semibold mb-2">Foto Saat Ini</div>
            <div class="flex flex-wrap gap-2">
            @foreach($menu->images as $img)
                <div class="relative">
                    <img src="{{ asset('storage/'.$img->path) }}" class="w-24 h-24 object-cover rounded-lg">
                    <form method="POST" action="{{ route('admin.menu.images.delete', [$menu->id, $img->id]) }}" class="absolute -top-2 -right-2">
                        @csrf @method('DELETE')
                        <button class="bg-red-500 text-white rounded-full w-6 h-6 text-xs">✕</button>
                    </form>
                </div>
            @endforeach
            </div>
        </div>
        @endif
    </div>
    <div class="flex gap-2">
        <button class="btn-primary" data-testid="save-menu">💾 Simpan</button>
        <a href="{{ route('admin.menu.index') }}" class="btn-secondary">Batal</a>
    </div>
</form>
@endsection
