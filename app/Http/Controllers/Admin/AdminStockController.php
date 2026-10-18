<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminStockController extends Controller
{
    public function index()
    {
        $menus = MenuItem::orderBy('name')->get();
        $history = StockHistory::with(['menuItem', 'user'])->latest()->take(30)->get();
        return view('admin.stock.index', compact('menus', 'history'));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'qty_change' => 'required|integer',
            'reason' => 'required|in:restock,koreksi',
        ]);
        $menu = MenuItem::findOrFail($id);
        $before = $menu->stock_qty;
        $after = max(0, $before + $data['qty_change']);
        $menu->stock_qty = $after;
        if ($after > 0) $menu->is_available = true;
        $menu->save();
        StockHistory::create([
            'menu_item_id' => $menu->id,
            'menu_name' => $menu->name,
            'qty_before' => $before,
            'qty_change' => $data['qty_change'],
            'qty_after' => $after,
            'reason' => $data['reason'],
            'changed_by' => Auth::id(),
        ]);
        return back()->with('success', 'Stok diperbarui.');
    }
}
