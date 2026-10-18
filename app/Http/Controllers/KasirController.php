<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Order;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function dashboard(Request $request)
    {
        $pending = Order::with(['table', 'items'])
            ->where('status', 'menunggu_konfirmasi')
            ->orderByDesc('created_at')->get();

        $active = Order::with(['table', 'items'])
            ->whereIn('status', ['dikonfirmasi', 'diproses', 'siap'])
            ->orderByDesc('created_at')->get();

        $done = Order::with(['table'])
            ->whereDate('updated_at', today())
            ->whereIn('status', ['selesai', 'dibatalkan'])
            ->orderByDesc('updated_at')->limit(30)->get();

        return view('kasir.dashboard', compact('pending', 'active', 'done'));
    }

    public function show(int $id)
    {
        $order = Order::with(['table', 'items.menuItem.images'])->findOrFail($id);
        return view('kasir.show', compact('order'));
    }

    public function checkNew(Request $request)
    {
        $lastId = (int) $request->input('last_id', 0);
        $orders = Order::with('table')
            ->where('id', '>', $lastId)
            ->where('status', 'menunggu_konfirmasi')
            ->orderBy('id')->get();
        return response()->json([
            'orders' => $orders->map(fn($o) => [
                'id' => $o->id,
                'order_number' => $o->order_number,
                'table_number' => $o->table->table_number,
                'total' => (float) $o->total_amount,
                'method' => $o->payment_method,
            ]),
        ]);
    }

    public function confirm(int $id)
    {
        $order = Order::with('items')->findOrFail($id);
        if ($order->status !== 'menunggu_konfirmasi') {
            return back()->with('error', 'Pesanan sudah diproses.');
        }
        DB::transaction(function () use ($order) {
            // decrement stock
            foreach ($order->items as $item) {
                $menu = $item->menuItem;
                if (!$menu) continue;
                $before = $menu->stock_qty;
                $menu->decrement('stock_qty', $item->quantity);
                $menu->refresh();
                if ($menu->stock_qty <= 0) {
                    $menu->update(['stock_qty' => 0, 'is_available' => false]);
                    Notification::create([
                        'type' => 'stok_habis',
                        'title' => 'Stok habis: ' . $menu->name,
                        'message' => 'Menu otomatis dinonaktifkan',
                        'data' => ['menu_id' => $menu->id],
                        'target_role' => 'admin',
                    ]);
                } elseif ($menu->stock_qty <= $menu->stock_alert_min) {
                    Notification::create([
                        'type' => 'stok_kritis',
                        'title' => 'Stok kritis: ' . $menu->name,
                        'message' => 'Sisa ' . $menu->stock_qty,
                        'data' => ['menu_id' => $menu->id],
                        'target_role' => 'admin',
                    ]);
                }
                StockHistory::create([
                    'menu_item_id' => $menu->id,
                    'menu_name' => $menu->name,
                    'qty_before' => $before,
                    'qty_change' => -$item->quantity,
                    'qty_after' => $menu->stock_qty,
                    'reason' => 'pesanan',
                    'order_id' => $order->id,
                    'changed_by' => Auth::id(),
                ]);
            }
            $order->update([
                'status' => 'diproses',
                'payment_status' => 'dikonfirmasi',
                'payment_confirmed_by' => Auth::id(),
                'payment_confirmed_at' => now(),
            ]);
        });
        return back()->with('success', 'Pembayaran dikonfirmasi.');
    }

    public function updateStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => 'required|in:diproses,siap,selesai',
        ]);
        $order = Order::findOrFail($id);
        $order->update(['status' => $data['status']]);
        return back()->with('success', 'Status diperbarui.');
    }

    public function cancel(Request $request, int $id)
    {
        $data = $request->validate([
            'reason' => 'required|string|max:200',
        ]);
        $order = Order::findOrFail($id);
        if (in_array($order->status, ['selesai', 'dibatalkan'])) {
            return back()->with('error', 'Pesanan tidak bisa dibatalkan.');
        }
        $order->update([
            'status' => 'dibatalkan',
            'payment_status' => 'dibatalkan',
            'cancellation_reason' => $data['reason'],
        ]);
        return back()->with('success', 'Pesanan dibatalkan.');
    }
}
