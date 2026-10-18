@extends('layouts.app')
@section('title', isset($bank) ? 'Edit Rekening' : 'Tambah Rekening')
@section('header', isset($bank) ? 'Edit Rekening' : 'Tambah Rekening')
@section('content')
<form method="POST" action="{{ isset($bank) ? route('admin.settings.bank-accounts.update', $bank->id) : route('admin.settings.bank-accounts.store') }}" class="card p-6 space-y-4 max-w-xl">
    @csrf @if(isset($bank)) @method('PUT') @endif
    <div><label class="label">Bank *</label><input name="bank_name" value="{{ old('bank_name', $bank->bank_name ?? '') }}" class="input" required placeholder="BCA / Mandiri / BNI"></div>
    <div><label class="label">No. Rekening *</label><input name="account_number" value="{{ old('account_number', $bank->account_number ?? '') }}" class="input font-mono-num" required></div>
    <div><label class="label">Atas Nama *</label><input name="account_name" value="{{ old('account_name', $bank->account_name ?? '') }}" class="input" required></div>
    <div><label class="label">Urutan</label><input type="number" name="sort_order" value="{{ old('sort_order', $bank->sort_order ?? 0) }}" class="input"></div>
    <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $bank->is_active ?? true) ? 'checked' : '' }}> Aktif</label>
    <div class="flex gap-2"><button class="btn-primary">💾 Simpan</button><a href="{{ route('admin.settings.payment') }}" class="btn-secondary">Batal</a></div>
</form>
@endsection
