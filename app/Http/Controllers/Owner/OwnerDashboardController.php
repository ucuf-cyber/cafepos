<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerDashboardController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to] = $this->resolveRange($request);
        return view('owner.dashboard', [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ]);
    }

    public function chartData(Request $request)
    {
        [$from, $to] = $this->resolveRange($request);
        $fromDt = $from->copy()->startOfDay();
        $toDt = $to->copy()->endOfDay();

        $base = Order::where('payment_status', 'dikonfirmasi')
            ->whereBetween('created_at', [$fromDt, $toDt]);

        $totalRevenue = (float) (clone $base)->sum('total_amount');
        $totalOrders = (clone $base)->count();
        $aov = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Previous period for delta
        $prevDays = $from->diffInDays($to) + 1;
        $prevFrom = $from->copy()->subDays($prevDays);
        $prevTo = $from->copy()->subDay()->endOfDay();
        $prevBase = Order::where('payment_status', 'dikonfirmasi')
            ->whereBetween('created_at', [$prevFrom, $prevTo]);
        $prevRevenue = (float) (clone $prevBase)->sum('total_amount');
        $prevOrders = (clone $prevBase)->count();
        $prevAov = $prevOrders > 0 ? $prevRevenue / $prevOrders : 0;

        $delta = fn($curr, $prev) => $prev > 0 ? round((($curr - $prev) / $prev) * 100, 1) : ($curr > 0 ? 100 : 0);

        // Payment breakdown
        $methods = (clone $base)
            ->select('payment_method', DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('payment_method')->get();
        $methodMap = ['cash' => 0, 'transfer' => 0, 'qris' => 0];
        foreach ($methods as $m) {
            $methodMap[$m->payment_method] = (float) $m->total;
        }
        $sumMethods = array_sum($methodMap) ?: 1;
        $payBreakdown = [];
        foreach ($methodMap as $k => $v) {
            $payBreakdown[] = [
                'method' => $k,
                'total' => $v,
                'percent' => round(($v / $sumMethods) * 100, 1),
            ];
        }

        // Daily revenue
        $dailyRows = (clone $base)
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('d')->orderBy('d')->get()->keyBy('d');
        $dailyRevenue = [];
        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $key = $d->toDateString();
            $row = $dailyRows->get($key);
            $dailyRevenue[] = [
                'date' => $key,
                'label' => $d->format('d M'),
                'total' => (float) ($row->total ?? 0),
                'count' => (int) ($row->cnt ?? 0),
            ];
        }

        // Top menus
        $topMenus = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', 'dikonfirmasi')
            ->whereBetween('orders.created_at', [$fromDt, $toDt])
            ->select('order_items.menu_name', DB::raw('SUM(order_items.quantity) as qty'), DB::raw('SUM(order_items.subtotal) as rev'))
            ->groupBy('order_items.menu_name')
            ->orderByDesc('qty')->limit(10)->get();

        // Revenue per category
        $byCategory = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('menu_items', 'menu_items.id', '=', 'order_items.menu_item_id')
            ->join('categories', 'categories.id', '=', 'menu_items.category_id')
            ->where('orders.payment_status', 'dikonfirmasi')
            ->whereBetween('orders.created_at', [$fromDt, $toDt])
            ->select('categories.name', DB::raw('SUM(order_items.subtotal) as total'))
            ->groupBy('categories.name')
            ->orderByDesc('total')->get();

        // Peak hours heatmap — compute in PHP for DB-agnostic compatibility
        $peakRows = (clone $base)
            ->select('created_at')
            ->get();
        $bucket = [];
        foreach ($peakRows as $r) {
            $dt = $r->created_at;
            $h = (int) $dt->format('H');
            $dow = (int) $dt->format('w') + 1; // MySQL DAYOFWEEK: 1=Sun..7=Sat
            $key = "$h-$dow";
            $bucket[$key] = ($bucket[$key] ?? 0) + 1;
        }
        $heatmap = [];
        foreach ($bucket as $k => $cnt) {
            [$h, $dow] = explode('-', $k);
            $heatmap[] = ['h' => (int)$h, 'dow' => (int)$dow, 'cnt' => $cnt];
        }

        // Stock
        $criticalStock = MenuItem::whereColumn('stock_qty', '<=', 'stock_alert_min')->count();
        $lowStock = MenuItem::whereColumn('stock_qty', '<=', 'stock_alert_min')
            ->orderBy('stock_qty')->limit(10)
            ->get(['id', 'name', 'stock_qty', 'stock_alert_min', 'is_available'])
            ->map(fn($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'stock_qty' => $m->stock_qty,
                'alert_min' => $m->stock_alert_min,
                'status' => $m->stock_qty <= 0 ? 'habis' : ($m->stock_qty <= $m->stock_alert_min ? 'kritis' : 'menipis'),
            ]);

        return response()->json([
            'range' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'kpi' => [
                'revenue' => $totalRevenue,
                'orders' => $totalOrders,
                'aov' => round($aov),
                'critical_stock' => $criticalStock,
                'delta_revenue' => $delta($totalRevenue, $prevRevenue),
                'delta_orders' => $delta($totalOrders, $prevOrders),
                'delta_aov' => $delta($aov, $prevAov),
            ],
            'payments' => $payBreakdown,
            'daily' => $dailyRevenue,
            'top_menus' => $topMenus,
            'by_category' => $byCategory,
            'heatmap' => $heatmap,
            'low_stock' => $lowStock,
        ]);
    }

    private function resolveRange(Request $request): array
    {
        $from = $request->filled('from') ? Carbon::parse($request->from) : Carbon::today()->subDays(6);
        $to = $request->filled('to') ? Carbon::parse($request->to) : Carbon::today();
        if ($from->gt($to)) [$from, $to] = [$to, $from];
        return [$from->startOfDay(), $to->endOfDay()];
    }
}
