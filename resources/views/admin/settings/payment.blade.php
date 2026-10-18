@extends('layouts.app')
@section('title','Pengaturan Pembayaran')
@section('header','Pengaturan Pembayaran')
@section('content')
<div class="grid lg:grid-cols-2 gap-4">
    <div class="card p-6">
        <div class="font-display text-lg font-semibold mb-4">Metode Pembayaran</div>
        <form method="POST" action="{{ route('admin.settings.payment.update') }}" class="space-y-3">
            @csrf
            <label class="flex items-center justify-between p-3 rounded-xl bg-coffee-50 dark:bg-coffee-700/50">
                <span>💵 Cash</span>
                <input type="checkbox" name="cash_active" value="1" {{ $setting->cash_active ? 'checked' : '' }}>
            </label>
            <label class="flex items-center justify-between p-3 rounded-xl bg-coffee-50 dark:bg-coffee-700/50">
                <span>🏦 Transfer Bank</span>
                <input type="checkbox" name="transfer_active" value="1" {{ $setting->transfer_active ? 'checked' : '' }}>
            </label>
            <label class="flex items-center justify-between p-3 rounded-xl bg-coffee-50 dark:bg-coffee-700/50">
                <span>📱 QRIS</span>
                <input type="checkbox" name="qris_active" value="1" {{ $setting->qris_active ? 'checked' : '' }}>
            </label>
            <button class="btn-primary w-full">💾 Simpan</button>
        </form>
    </div>
    <div class="card p-6">
        <div class="font-display text-lg font-semibold mb-4">Gambar QRIS</div>
        @if($setting->qris_image_path)
            <img src="{{ asset('storage/' . $setting->qris_image_path) }}" class="rounded-xl max-w-xs mx-auto mb-4">
        @else
            <div class="text-coffee-500 mb-4">Belum ada gambar QRIS.</div>
        @endif
        <form method="POST" action="{{ route('admin.settings.qris') }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <input type="file" name="qris_image" accept="image/*" class="input" required>
            <button class="btn-primary w-full">🔄 Upload QRIS</button>
        </form>
    </div>
    <div class="card p-6 lg:col-span-2">
        <div class="flex justify-between items-center mb-4">
            <div class="font-display text-lg font-semibold">Rekening Bank</div>
            <a href="{{ route('admin.settings.bank-accounts.create') }}" class="btn-primary btn-sm">+ Tambah Rekening</a>
        </div>
        <table class="w-full text-sm">
            <thead class="text-left text-coffee-500"><tr><th class="py-2">Bank</th><th>No. Rekening</th><th>Atas Nama</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @foreach($banks as $b)
            <tr class="border-t border-coffee-100 dark:border-coffee-700">
                <td class="py-2 font-semibold">{{ $b->bank_name }}</td>
                <td class="font-mono-num">{{ $b->account_number }}</td>
                <td>{{ $b->account_name }}</td>
                <td>@if($b->is_active)<span class="badge-green">Aktif</span>@else<span class="badge-gray">Nonaktif</span>@endif</td>
                <td class="space-x-2">
                    <a href="{{ route('admin.settings.bank-accounts.edit', $b->id) }}" class="btn-ghost btn-sm">✏️</a>
                    <form method="POST" action="{{ route('admin.settings.bank-accounts.destroy', $b->id) }}" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn-danger btn-sm">🗑</button></form>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
