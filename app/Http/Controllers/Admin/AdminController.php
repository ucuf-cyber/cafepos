<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Table;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $today = today();
        $stats = [
            'orders_today' => Order::whereDate('created_at', $today)->count(),
            'revenue_today' => (float) Order::whereDate('created_at', $today)
                ->where('payment_status', 'dikonfirmasi')->sum('total_amount'),
            'menu_active' => MenuItem::where('is_available', true)->count(),
            'tables' => Table::count(),
            'critical_stock' => MenuItem::whereColumn('stock_qty', '<=', 'stock_alert_min')->count(),
            'users' => User::where('is_active', true)->count(),
        ];
        $recent = Order::with('table')->latest()->take(10)->get();
        $lowStock = MenuItem::whereColumn('stock_qty', '<=', 'stock_alert_min')
            ->orderBy('stock_qty')->take(8)->get();
        return view('admin.dashboard', compact('stats', 'recent', 'lowStock'));
    }
}
