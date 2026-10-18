<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentSetting;
use App\Models\StockHistory;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function menu(string $token)
    {
        $table = Table::where('qr_token', $token)->where('is_active', true)->firstOrFail();
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $menus = MenuItem::with(['images', 'category'])
            ->orderBy('sort_order')
            ->get();
        $settings = PaymentSetting::current();
        $banks = BankAccount::where('is_active', true)->orderBy('sort_order')->get();

        return view('customer.menu', compact('table', 'categories', 'menus', 'settings', 'banks'));
    }

    public function store(Request $request, string $token)
    {
        $table = Table::where('qr_token', $token)->where('is_active', true)->firstOrFail();

        $data = $request->validate([
            'payment_method' => 'required|in:cash,transfer,qris',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.variant' => 'nullable|array',
            'items.*.notes' => 'nullable|string|max:500',
        ]);

        $settings = PaymentSetting::current();
        $methodActive = [
            'cash' => $settings->cash_active,
            'transfer' => $settings->transfer_active,
            'qris' => $settings->qris_active,
        ];
        if (!($methodActive[$data['payment_method']] ?? false)) {
            return back()->withErrors(['payment_method' => 'Metode pembayaran tidak aktif.']);
        }

        $order = DB::transaction(function () use ($data, $table) {
            $total = 0;
            $itemsPrepared = [];
            foreach ($data['items'] as $row) {
                $menu = MenuItem::lockForUpdate()->findOrFail($row['menu_item_id']);
                if (!$menu->is_available || $menu->stock_qty < $row['quantity']) {
                    abort(422, "Stok {$menu->name} tidak mencukupi.");
                }
                $subtotal = $menu->price * $row['quantity'];
                $total += $subtotal;
                $itemsPrepared[] = [
                    'menu' => $menu,
                    'qty' => $row['quantity'],
                    'subtotal' => $subtotal,
                    'variant' => $row['variant'] ?? null,
                    'notes' => $row['notes'] ?? null,
                ];
            }

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'table_id' => $table->id,
                'status' => 'menunggu_konfirmasi',
                'payment_method' => $data['payment_method'],
                'payment_status' => 'menunggu',
                'notes' => $data['notes'] ?? null,
                'total_amount' => $total,
            ]);

            foreach ($itemsPrepared as $row) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $row['menu']->id,
                    'menu_name' => $row['menu']->name,
                    'unit_price' => $row['menu']->price,
                    'quantity' => $row['qty'],
                    'variant' => $row['variant'],
                    'notes' => $row['notes'],
                    'subtotal' => $row['subtotal'],
                ]);
            }

            Notification::create([
                'type' => 'pesanan_baru',
                'title' => 'Pesanan Baru — Meja ' . $table->table_number,
                'message' => "Order {$order->order_number} • Rp " . number_format((float) $total, 0, ',', '.') . ' • ' . strtoupper($data['payment_method']),
                'data' => ['order_id' => $order->id, 'table_id' => $table->id],
                'target_role' => 'kasir',
            ]);

            return $order;
        });

        return redirect()->route('customer.track', ['token' => $token, 'id' => $order->id]);
    }

    public function track(string $token, int $id)
    {
        $table = Table::where('qr_token', $token)->firstOrFail();
        $order = Order::with('items')->where('table_id', $table->id)->findOrFail($id);
        return view('customer.track', compact('table', 'order'));
    }

    public function status(string $token, int $id)
    {
        $table = Table::where('qr_token', $token)->firstOrFail();
        $order = Order::where('table_id', $table->id)->findOrFail($id);
        return response()->json([
            'status' => $order->status,
            'status_label' => $order->status_label,
            'payment_status' => $order->payment_status,
            'updated_at' => $order->updated_at?->toIso8601String(),
        ]);
    }

    public function markPaid(string $token, int $id)
    {
        $table = Table::where('qr_token', $token)->firstOrFail();
        $order = Order::where('table_id', $table->id)->findOrFail($id);
        if ($order->payment_status === 'menunggu') {
            $order->update(['customer_paid_claim' => true]);
            Notification::create([
                'type' => 'pesanan_qris_notify',
                'title' => 'Pelanggan klaim sudah bayar QRIS — Meja ' . $table->table_number,
                'message' => "Order {$order->order_number} • verifikasi mutasi QRIS",
                'data' => ['order_id' => $order->id],
                'target_role' => 'kasir',
            ]);
        }
        return back();
    }
}
