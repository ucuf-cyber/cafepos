@extends('layouts.app')
@section('title','Pengguna')
@section('header','Manajemen Pengguna')
@section('content')
<div class="flex justify-end mb-4"><a href="{{ route('admin.users.create') }}" class="btn-primary" data-testid="add-user">+ Tambah Pengguna</a></div>
<div class="card overflow-hidden">
<table class="w-full text-sm">
    <thead class="bg-coffee-50 dark:bg-coffee-700/50 text-left"><tr><th class="px-4 py-3">Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    @foreach($users as $u)
    <tr class="border-t border-coffee-100 dark:border-coffee-700">
        <td class="px-4 py-2 font-semibold">{{ $u->name }}</td>
        <td>{{ $u->email }}</td>
        <td><span class="badge-blue">{{ ucfirst($u->role) }}</span></td>
        <td>@if($u->is_active)<span class="badge-green">Aktif</span>@else<span class="badge-gray">Nonaktif</span>@endif</td>
        <td class="space-x-2">
            <a href="{{ route('admin.users.edit', $u->id) }}" class="btn-ghost btn-sm">✏️</a>
            @if($u->id !== auth()->id())
            <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" class="inline" onsubmit="return confirm('Hapus pengguna?')">@csrf @method('DELETE')<button class="btn-danger btn-sm">🗑</button></form>
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
</div>
@endsection
