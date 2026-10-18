@php
    $role = auth()->user()->role ?? null;
@endphp
@if($role === 'kasir' || $role === 'admin')
<a href="{{ route('kasir.dashboard') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-coffee-700 transition {{ request()->routeIs('kasir.*') ? 'bg-coffee-700 text-caramel-400' : '' }}" data-testid="nav-kasir">
    🎯 Kasir Dashboard
</a>
@endif
@if($role === 'admin')
<a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-coffee-700 transition {{ request()->routeIs('admin.dashboard') ? 'bg-coffee-700 text-caramel-400' : '' }}" data-testid="nav-admin">📊 Admin Dashboard</a>
<a href="{{ route('admin.menu.index') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-coffee-700 transition {{ request()->routeIs('admin.menu.*') ? 'bg-coffee-700 text-caramel-400' : '' }}" data-testid="nav-menu">🍽 Menu</a>
<a href="{{ route('admin.categories.index') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-coffee-700 transition {{ request()->routeIs('admin.categories.*') ? 'bg-coffee-700 text-caramel-400' : '' }}" data-testid="nav-cat">🏷 Kategori</a>
<a href="{{ route('admin.tables.index') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-coffee-700 transition {{ request()->routeIs('admin.tables.*') ? 'bg-coffee-700 text-caramel-400' : '' }}" data-testid="nav-tables">🪑 Meja & QR</a>
<a href="{{ route('admin.stock.index') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-coffee-700 transition {{ request()->routeIs('admin.stock.*') ? 'bg-coffee-700 text-caramel-400' : '' }}" data-testid="nav-stock">📦 Stok</a>
<a href="{{ route('admin.users.index') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-coffee-700 transition {{ request()->routeIs('admin.users.*') ? 'bg-coffee-700 text-caramel-400' : '' }}" data-testid="nav-users">👥 Pengguna</a>
<a href="{{ route('admin.settings.payment') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-coffee-700 transition {{ request()->routeIs('admin.settings.*') ? 'bg-coffee-700 text-caramel-400' : '' }}" data-testid="nav-settings">⚙️ Pengaturan Bayar</a>
@endif
@if($role === 'owner')
<a href="{{ route('owner.dashboard') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-coffee-700 transition {{ request()->routeIs('owner.dashboard') ? 'bg-coffee-700 text-caramel-400' : '' }}" data-testid="nav-owner">📈 Dashboard Keuangan</a>
<a href="{{ route('owner.stock') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-coffee-700 transition {{ request()->routeIs('owner.stock') ? 'bg-coffee-700 text-caramel-400' : '' }}" data-testid="nav-owner-stock">📦 Monitor Stok</a>
@endif
