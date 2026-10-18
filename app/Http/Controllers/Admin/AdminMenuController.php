<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuImage;
use App\Models\MenuItem;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminMenuController extends Controller
{
    public function index(Request $request)
    {
        $q = MenuItem::with(['category', 'primaryImage']);
        if ($request->filled('search')) {
            $q->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category_id')) {
            $q->where('category_id', $request->category_id);
        }
        $menus = $q->orderBy('sort_order')->paginate(20)->withQueryString();
        $categories = Category::orderBy('sort_order')->get();
        return view('admin.menu.index', compact('menus', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.menu.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validateMenu($request);
        $menu = MenuItem::create($data);
        $this->saveImages($request, $menu);
        return redirect()->route('admin.menu.index')->with('success', 'Menu ditambahkan.');
    }

    public function edit(int $id)
    {
        $menu = MenuItem::with('images')->findOrFail($id);
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.menu.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, int $id)
    {
        $menu = MenuItem::findOrFail($id);
        $data = $this->validateMenu($request);
        $menu->update($data);
        $this->saveImages($request, $menu);
        return redirect()->route('admin.menu.index')->with('success', 'Menu diperbarui.');
    }

    public function destroy(int $id)
    {
        $menu = MenuItem::findOrFail($id);
        foreach ($menu->images as $img) {
            Storage::disk('public')->delete($img->path);
        }
        $menu->delete();
        return back()->with('success', 'Menu dihapus.');
    }

    public function uploadImages(Request $request, int $id)
    {
        $menu = MenuItem::findOrFail($id);
        $this->saveImages($request, $menu);
        return back()->with('success', 'Foto diunggah.');
    }

    public function deleteImage(int $id, int $imageId)
    {
        $img = MenuImage::where('menu_item_id', $id)->findOrFail($imageId);
        Storage::disk('public')->delete($img->path);
        $img->delete();
        return back()->with('success', 'Foto dihapus.');
    }

    public function updateStock(Request $request, int $id)
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

    private function validateMenu(Request $request): array
    {
        return $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'is_available' => 'nullable|boolean',
            'stock_qty' => 'required|integer|min:0',
            'stock_alert_min' => 'required|integer|min:0',
            'labels' => 'nullable|array',
            'variants' => 'nullable|array',
            'sort_order' => 'nullable|integer',
        ]) + ['is_available' => $request->boolean('is_available', true)];
    }

    private function saveImages(Request $request, MenuItem $menu): void
    {
        if (!$request->hasFile('images')) return;
        $existing = $menu->images()->count();
        foreach ($request->file('images') as $i => $file) {
            if ($existing + $i >= 5) break;
            $path = $file->store('menu-images', 'public');
            MenuImage::create([
                'menu_item_id' => $menu->id,
                'path' => $path,
                'sort_order' => $existing + $i,
                'is_primary' => ($existing + $i) === 0,
            ]);
        }
    }
}
