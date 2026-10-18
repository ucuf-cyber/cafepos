<table border="1">
<tr><th colspan="5">CaféPos — {{ $from->format('Y-m-d') }} sd {{ $to->format('Y-m-d') }}</th></tr>
<tr><th>Order</th><th>Waktu</th><th>Meja</th><th>Metode</th><th>Total</th></tr>
@foreach($orders as $o)
<tr><td>{{ $o->order_number }}</td><td>{{ $o->created_at->format('Y-m-d H:i') }}</td><td>{{ $o->table->table_number ?? '-' }}</td><td>{{ strtoupper($o->payment_method) }}</td><td>{{ $o->total_amount }}</td></tr>
@endforeach
<tr><td colspan="4"><b>Total</b></td><td><b>{{ $totalRevenue }}</b></td></tr>
</table>
