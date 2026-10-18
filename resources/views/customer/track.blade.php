@extends('layouts.customer')
@section('title', 'Pesanan ' . $order->order_number)
@section('content')
<div x-data="trackOrder()" x-init="startPolling()" class="min-h-screen px-4 py-8 max-w-lg mx-auto">
    <div class="card p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <div class="text-xs uppercase tracking-widest text-coffee-500">CaféPos · Meja {{ $order->table->table_number }}</div>
                <div class="font-display text-3xl font-semibold mt-1 text-coffee-900 dark:text-cream-50">{{ $order->order_number }}</div>
            </div>
            <div class="text-right">
                <div class="text-xs text-coffee-500">Total</div>
                <div class="font-mono-num font-bold text-2xl text-caramel-600">Rp {{ number_format((float)$order->total_amount, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="mt-4 text-xs">
            <span class="badge-amber" x-show="status==='menunggu_konfirmasi'">⏳ Menunggu Konfirmasi Kasir</span>
            <span class="badge-blue"  x-show="status==='dikonfirmasi'">✅ Pembayaran Dikonfirmasi</span>
            <span class="badge-purple" x-show="status==='diproses'">🔄 Sedang Diproses</span>
            <span class="badge-green" x-show="status==='siap'">🔔 Pesanan Siap</span>
            <span class="badge-gray" x-show="status==='selesai'">✓ Selesai</span>
            <span class="badge-red" x-show="status==='dibatalkan'">❌ Dibatalkan</span>
        </div>
    </div>

    <!-- Method info -->
    @if($order->payment_method === 'qris')
    <div class="card p-6 mb-6 text-center">
        <div class="text-xs uppercase tracking-widest text-coffee-500">📱 Bayar dengan QRIS</div>
        <img src="{{ url('/payment/qris') }}" onerror="this.style.display='none'" class="mx-auto my-4 rounded-xl max-w-[220px]" alt="QRIS">
        <div class="text-coffee-500 text-sm">Total: <span class="font-mono-num font-bold text-coffee-900 dark:text-cream-50">Rp {{ number_format((float)$order->total_amount, 0, ',', '.') }}</span></div>
        @if($order->payment_status === 'menunggu')
        <form method="POST" action="{{ route('customer.paid', ['token'=>$order->table->qr_token, 'id'=>$order->id]) }}" class="mt-4">
            @csrf
            <button class="btn-primary w-full" data-testid="paid-btn" {{ $order->customer_paid_claim ? 'disabled' : '' }}>
                {{ $order->customer_paid_claim ? '✓ Sudah Diberitahu' : '✅ Sudah Bayar — Beritahu Kasir' }}
            </button>
        </form>
        @endif
    </div>
    @elseif($order->payment_method === 'transfer')
    <div class="card p-6 mb-6">
        <div class="text-xs uppercase tracking-widest text-coffee-500 mb-3">🏦 Transfer Bank</div>
        <div class="text-sm text-coffee-600 dark:text-cream-100/80 mb-3">Transfer ke salah satu rekening berikut, lalu tunjukkan bukti ke kasir.</div>
        <div id="bank-list" class="space-y-2 text-sm"></div>
        <script>
        fetch('{{ url('/payment/bank-accounts') }}').then(r=>r.json()).then(banks=>{
            document.getElementById('bank-list').innerHTML = banks.map(b=>`
                <div class="p-3 rounded-xl bg-coffee-50 dark:bg-coffee-700/50">
                    <div class="text-xs uppercase">${b.bank_name}</div>
                    <div class="font-mono-num font-bold text-lg">${b.account_number}</div>
                    <div class="text-xs">a.n. ${b.account_name}</div>
                </div>`).join('');
        });
        </script>
    </div>
    @else
    <div class="card p-6 mb-6 text-center">
        <div class="text-4xl mb-2">💵</div>
        <div class="font-semibold">Pembayaran Cash</div>
        <div class="text-sm text-coffee-500 mt-1">Siapkan uang tunai. Kasir akan menghampiri meja Anda.</div>
    </div>
    @endif

    <!-- Items -->
    <div class="card p-5 mb-6">
        <div class="font-semibold mb-3">Detail Pesanan</div>
        @foreach($order->items as $it)
        <div class="flex justify-between py-2 border-b border-coffee-100 dark:border-coffee-700 last:border-0">
            <div>
                <div class="text-sm font-semibold">{{ $it->menu_name }}</div>
                <div class="text-xs text-coffee-500">{{ $it->quantity }} × Rp {{ number_format((float)$it->unit_price,0,',','.') }}</div>
            </div>
            <div class="font-mono-num text-sm">Rp {{ number_format((float)$it->subtotal,0,',','.') }}</div>
        </div>
        @endforeach
    </div>

    <a href="{{ route('customer.menu', $order->table->qr_token) }}" class="btn-secondary w-full" data-testid="back-menu">← Kembali ke Menu</a>
</div>

<script>
function trackOrder() {
    return {
        status: @json($order->status),
        startPolling() {
            setInterval(async () => {
                const r = await fetch('{{ route('customer.status', ['token'=>$order->table->qr_token, 'id'=>$order->id]) }}');
                const j = await r.json();
                if (j.status !== this.status) {
                    this.status = j.status;
                    window.toast('Status: ' + j.status_label, 'info');
                }
            }, 5000);
        }
    }
}
</script>
@endsection
