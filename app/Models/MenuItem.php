<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'category_id', 'name', 'description', 'price',
        'is_available', 'stock_qty', 'stock_alert_min',
        'labels', 'variants', 'sort_order',
    ];

    protected $casts = [
        'labels' => 'array',
        'variants' => 'array',
        'is_available' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(MenuImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(MenuImage::class)->where('is_primary', true);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock_qty <= 0) return 'habis';
        if ($this->stock_qty <= $this->stock_alert_min) return 'kritis';
        if ($this->stock_qty <= $this->stock_alert_min * 2) return 'menipis';
        return 'aman';
    }
}
