<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Laporan CaféPos</title>
<style>
body { font-family: Arial, sans-serif; font-size: 11pt; color: #2A1F1A; }
h1 { font-family: serif; }
table { width: 100%; border-collapse: collapse; margin-top: 8pt; }
th, td { border: 1px solid #ccc; padding: 5pt; text-align: left; }
th { background: #f5ede0; }
.kpi { display: flex; gap: 8pt; margin: 8pt 0; }
.kpi > div { flex:1; border: 1px solid #ccc; padding: 8pt; border-radius: 6pt; }
.muted { color:#888; font-size: 9pt; }
</style></head><body>
<h1>☕ CaféPos — Laporan Keuangan</h1>
<div class="muted">Periode: {{ $from->format('d/m/Y') }} — {{ $to->format('d/m/Y') }}</div>
<div class="kpi">
    <div><div class="muted">Total Revenue</div><b style="font-size:14pt">Rp {{ number_format($totalRevenue,0,',','.') }}</b></div>
    <div><div class="muted">Total Transaksi</div><b style="font-size:14pt">{{ $totalOrders }}</b></div>
    <div><div class="muted">AOV</div><b style="font-size:14pt">Rp {{ number_format($aov,0,',','.') }}</b></div>
</div>

<h3>Breakdown Metode Pembayaran</h3>
<table><tr><th>Metode</th><th>Jumlah Tx</th><th>Total</th></tr>
@foreach(['cash','transfer','qris'] as $m)
@php $row = $methods->get($m); @endphp
<tr><td>{{ strtoupper($m) }}</td><td>{{ $row['count'] ?? 0 }}</td><td>Rp {{ number_format($row['total'] ?? 0, 0, ',', '.') }}</td></tr>
@endforeach
</table>

<h3>Top Menu Terlaris</h3>
<table><tr><th>Menu</th><th>Qty Terjual</th><th>Revenue</th></tr>
@foreach($topMenus as $tm)
<tr><td>{{ $tm->menu_name }}</td><td>{{ $tm->qty }}</td><td>Rp {{ number_format($tm->rev,0,',','.') }}</td></tr>
@endforeach
</table>

<h3>Detail Transaksi</h3>
<table><tr><th>Order</th><th>Waktu</th><th>Meja</th><th>Metode</th><th>Total</th></tr>
@foreach($orders as $o)
<tr>
    <td>{{ $o->order_number }}</td>
    <td>{{ $o->created_at->format('d/m H:i') }}</td>
    <td>{{ $o->table->table_number ?? '-' }}</td>
    <td>{{ strtoupper($o->payment_method) }}</td>
    <td>Rp {{ number_format((float)$o->total_amount,0,',','.') }}</td>
</tr>
@endforeach
</table>
</body></html>
