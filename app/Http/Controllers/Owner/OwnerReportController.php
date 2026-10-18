<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerReportController extends Controller
{
    public function export(Request $request)
    {
        $format = $request->input('format', 'pdf');
        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : Carbon::today()->subDays(6)->startOfDay();
        $to = $request->filled('to') ? Carbon::parse($request->to)->endOfDay() : Carbon::today()->endOfDay();

        $orders = Order::with(['table', 'items'])
            ->where('payment_status', 'dikonfirmasi')
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')->get();

        $totalRevenue = (float) $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $aov = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $topMenus = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', 'dikonfirmasi')
            ->whereBetween('orders.created_at', [$from, $to])
            ->select('order_items.menu_name', DB::raw('SUM(order_items.quantity) as qty'), DB::raw('SUM(order_items.subtotal) as rev'))
            ->groupBy('order_items.menu_name')
            ->orderByDesc('qty')->limit(20)->get();

        $methods = $orders->groupBy('payment_method')->map(fn($g) => [
            'total' => $g->sum('total_amount'),
            'count' => $g->count(),
        ]);

        $payload = compact('orders', 'totalRevenue', 'totalOrders', 'aov', 'topMenus', 'methods', 'from', 'to');

        if ($format === 'csv') {
            $filename = "laporan_cafepos_{$from->toDateString()}_{$to->toDateString()}.csv";
            return response()->streamDownload(function () use ($orders) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Order', 'Tanggal', 'Meja', 'Metode', 'Total']);
                foreach ($orders as $o) {
                    fputcsv($out, [
                        $o->order_number,
                        $o->created_at->format('Y-m-d H:i'),
                        $o->table?->table_number,
                        strtoupper($o->payment_method),
                        $o->total_amount,
                    ]);
                }
                fclose($out);
            }, $filename, ['Content-Type' => 'text/csv']);
        }

        if ($format === 'excel') {
            // Simple Excel via CSV with .xls extension as fallback (Laravel Excel needs binary)
            $filename = "laporan_cafepos_{$from->toDateString()}_{$to->toDateString()}.xls";
            $html = view('owner.reports.excel', $payload)->render();
            return response($html, 200, [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        // PDF
        $pdf = Pdf::loadView('owner.reports.pdf', $payload)->setPaper('a4', 'portrait');
        return $pdf->download("laporan_cafepos_{$from->toDateString()}_{$to->toDateString()}.pdf");
    }
}
