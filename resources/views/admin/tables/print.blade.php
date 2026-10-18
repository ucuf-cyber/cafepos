<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>QR Meja {{ $table->table_number }}</title>
<style>
    @page { size: 10cm 10cm; margin: 0; }
    body { font-family: 'Helvetica', Arial, sans-serif; margin: 0; padding: 1cm; text-align: center; }
    h1 { font-size: 18pt; margin: 0 0 0.3cm; }
    .qr { display: inline-block; padding: 0.3cm; background:#fff; }
    .meja { font-size: 14pt; font-weight: bold; margin-top: 0.3cm; }
    .url { font-size: 8pt; color: #555; margin-top: 0.2cm; word-break: break-all; }
    .actions { margin-top: 1cm; }
    @media print { .actions { display: none; } }
    button { padding: 8px 16px; }
</style>
</head>
<body>
<h1>☕ CaféPos</h1>
<div class="qr">{!! $qrSvg !!}</div>
<div class="meja">MEJA NO. {{ $table->table_number }}</div>
<div>Scan untuk memesan</div>
<div class="url">{{ $url }}</div>
<div class="actions"><button onclick="window.print()">🖨 Print</button></div>
</body>
</html>
