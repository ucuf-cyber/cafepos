<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    protected $table = 'stock_history';

    protected $fillable = [
        'menu_item_id', 'menu_name', 'qty_before', 'qty_change',
        'qty_after', 'reason', 'order_id', 'changed_by',
    ];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
