@extends('layouts.app')
@section('title', 'Kasir Dashboard')
@section('header', 'Dashboard Kasir')
@section('subheader', 'Konfirmasi pembayaran & kelola pesanan')
@section('content')
<div x-data="kasirDash({{ $pending->max('id') ?? 0 }})" x-init="poll()" class="space-y-6">
    <div class="flex flex-wrap gap-2">
        <button @click="tab='pending'" :class="tab==='pending' ? 'bg-caramel-500 text-white' : 'bg-white dark:bg-coffee-800'" class="px-5 py-2.5 rounded-xl font-semibold shadow-sm transition" data-testid="tab-pending">⏳ Menunggu ({{ $pending->count() }})</button>
        <button @click="tab='active'"  :class="tab==='active'  ? 'bg-caramel-500 text-white' : 'bg-white dark:bg-coffee-800'" class="px-5 py-2.5 rounded-xl font-semibold shadow-sm transition" data-testid="tab-active">🔵 Aktif ({{ $active->count() }})</button>
        <button @click="tab='done'"    :class="tab==='done'    ? 'bg-caramel-500 text-white' : 'bg-white dark:bg-coffee-800'" class="px-5 py-2.5 rounded-xl font-semibold shadow-sm transition" data-testid="tab-done">✅ Selesai ({{ $done->count() }})</button>
    </div>

    <div x-show="tab==='pending'" class="grid lg:grid-cols-2 gap-4">
        @forelse($pending as $o)
        <div class="card p-5" data-testid="order-{{ $o->id }}">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <div class="text-xs text-coffee-500">Meja {{ $o->table->table_number }} · {{ $o->order_number }}</div>
                    <div class="font-display text-lg font-semibold">{{ $o->created_at->diffForHumans() }}</div>
                </div>
                <div class="text-right">
                    @if($o->payment_method==='cash') <span class="badge-amber">💵 Cash</span>
                    @elseif($o->payment_method==='transfer') <span class="badge-blue">🏦 Transfer</span>
                    @else <span class="badge-purple">📱 QRIS</span>
                    @endif
                    @if($o->customer_paid_claim) <span class="badge-green ml-1">✓ Klaim Bayar</span> @endif
                </div>
            </div>
            <div class="space-y-1 text-sm mb-3">
                @foreach($o->items as $it)
                <div class="flex justify-between">
                    <span>{{ $it->quantity }}× {{ $it->menu_name }}</span>
                    <span class="font-mono-num text-xs text-coffee-500">Rp {{ number_format((float)$it->subtotal,0,',','.') }}</span>
                </div>
                @endforeach
            </div>
            @if($o->notes)<div class="text-xs italic text-coffee-500 mb-3">📝 {{ $o->notes }}</div>@endif
            <div class="flex items-center justify-between border-t border-coffee-100 dark:border-coffee-700 pt-3">
                <div class="font-mono-num font-bold text-lg text-caramel-600">Rp {{ number_format((float)$o->total_amount,0,',','.') }}</div>
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('kasir.orders.confirm', $o->id) }}">@csrf @method('PATCH')
                        <button class="btn-success btn-sm" data-testid="confirm-{{ $o->id }}">✓ Konfirmasi</button>
                    </form>
                    <button @click="cancelId={{ $o->id }}" class="btn-danger btn-sm" data-testid="cancel-{{ $o->id }}">✕ Batalkan</button>
                </div>
            </div>
        </div>
        @empty
        <div class="card p-8 text-center text-coffee-500 col-span-full">Belum ada pesanan menunggu konfirmasi 🌿</div>
        @endforelse
    </div>

    <div x-show="tab==='active'" class="grid lg:grid-cols-2 gap-4">
        @forelse($active as $o)
        <div class="card p-5">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <div class="text-xs text-coffee-500">Meja {{ $o->table->table_number }} · {{ $o->order_number }}</div>
                    <div class="font-display text-lg font-semibold">{{ $o->status_label }}</div>
                </div>
                <span class="badge-blue">{{ strtoupper($o->payment_method) }}</span>
            </div>
            <div class="space-y-1 text-sm mb-3">
                @foreach($o->items as $it)
                <div>{{ $it->quantity }}× {{ $it->menu_name }}</div>
                @endforeach
            </div>
            <div class="flex items-center justify-between border-t border-coffee-100 dark:border-coffee-700 pt-3">
                <div class="font-mono-num font-bold">Rp {{ number_format((float)$o->total_amount,0,',','.') }}</div>
                <form method="POST" action="{{ route('kasir.orders.status', $o->id) }}" class="flex gap-2">
                    @csrf @method('PATCH')
                    @if($o->status === 'diproses')
                        <button name="status" value="siap" class="btn-success btn-sm">→ Siap</button>
                    @elseif($o->status === 'siap')
                        <button name="status" value="selesai" class="btn-secondary btn-sm">→ Selesai</button>
                    @endif
                </form>
            </div>
        </div>
        @empty
        <div class="card p-8 text-center text-coffee-500 col-span-full">Tidak ada pesanan aktif</div>
        @endforelse
    </div>

    <div x-show="tab==='done'" class="card p-5">
        <table class="w-full text-sm">
            <thead class="text-left text-coffee-500"><tr>
                <th class="py-2">Order</th><th>Meja</th><th>Metode</th><th>Total</th><th>Status</th><th>Waktu</th>
            </tr></thead>
            <tbody>
            @forelse($done as $o)
                <tr class="border-t border-coffee-100 dark:border-coffee-700">
                    <td class="py-2 font-mono-num">{{ $o->order_number }}</td>
                    <td>{{ $o->table->table_number }}</td>
                    <td>{{ strtoupper($o->payment_method) }}</td>
                    <td class="font-mono-num">Rp {{ number_format((float)$o->total_amount,0,',','.') }}</td>
                    <td>
                        @if($o->status==='selesai')<span class="badge-gray">Selesai</span>@else<span class="badge-red">Dibatalkan</span>@endif
                    </td>
                    <td>{{ $o->updated_at->format('H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-6 text-center text-coffee-500">Belum ada pesanan selesai hari ini</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Cancel modal -->
    <div x-show="cancelId" x-transition.opacity class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
        <div class="card p-6 w-full max-w-md">
            <div class="font-display text-xl font-semibold mb-3">Batalkan Pesanan</div>
            <form :action="`/kasir/orders/${cancelId}/cancel`" method="POST">
                @csrf @method('PATCH')
                <label class="label">Alasan</label>
                <select name="reason" class="input" required>
                    <option value="">— Pilih alasan —</option>
                    <option value="Pelanggan membatalkan">Pelanggan membatalkan</option>
                    <option value="Stok tidak tersedia">Stok tidak tersedia</option>
                    <option value="Pembayaran tidak diterima">Pembayaran tidak diterima</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
                <div class="flex gap-2 mt-4">
                    <button type="button" @click="cancelId=null" class="btn-secondary flex-1">Batal</button>
                    <button class="btn-danger flex-1">Batalkan Pesanan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function kasirDash(lastId) {
    return {
        tab: 'pending',
        cancelId: null,
        lastId: lastId,
        poll() {
            setInterval(async () => {
                const r = await fetch('{{ route('kasir.orders.check') }}?last_id=' + this.lastId);
                const j = await r.json();
                if (j.orders.length > 0) {
                    j.orders.forEach(o => {
                        window.toast(`🔔 Pesanan Baru — Meja ${o.table_number} — ${o.order_number} — Rp ${o.total.toLocaleString('id-ID')}`, 'info');
                        if (o.id > this.lastId) this.lastId = o.id;
                    });
                    try { new Audio('data:audio/wav;base64,UklGRn9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQ==').play(); } catch(e){}
                    setTimeout(()=>location.reload(), 1500);
                }
            }, 5000);
        }
    }
}
</script>
@endsection
