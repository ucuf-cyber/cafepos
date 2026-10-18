<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;

class OwnerStockController extends Controller
{
    public function index()
    {
        $menus = MenuItem::with('category')->orderBy('stock_qty')->get();
        return view('owner.stock', compact('menus'));
    }
}
