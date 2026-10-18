@extends('layouts.app')
@section('title', isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna')
@section('header', isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna')
@section('content')
<form method="POST" action="{{ isset($user) ? route('admin.users.update', $user->id) : route('admin.users.store') }}" class="card p-6 space-y-4 max-w-xl">
    @csrf @if(isset($user)) @method('PUT') @endif
    <div><label class="label">Nama *</label><input name="name" value="{{ old('name', $user->name ?? '') }}" class="input" required></div>
    <div><label class="label">Email *</label><input name="email" type="email" value="{{ old('email', $user->email ?? '') }}" class="input" required></div>
    <div><label class="label">Password {{ isset($user) ? '(kosongkan jika tidak diubah)' : '*' }}</label><input name="password" type="password" class="input" {{ isset($user) ? '' : 'required' }} minlength="6"></div>
    <div><label class="label">Role *</label>
        <select name="role" class="input" required>
            <option value="owner" {{ ($user->role ?? '')==='owner' ? 'selected' : '' }}>Owner</option>
            <option value="admin" {{ ($user->role ?? '')==='admin' ? 'selected' : '' }}>Admin</option>
            <option value="kasir" {{ ($user->role ?? 'kasir')==='kasir' ? 'selected' : '' }}>Kasir</option>
        </select>
    </div>
    <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}> Aktif</label>
    <div class="flex gap-2"><button class="btn-primary">💾 Simpan</button><a href="{{ route('admin.users.index') }}" class="btn-secondary">Batal</a></div>
</form>
@endsection
