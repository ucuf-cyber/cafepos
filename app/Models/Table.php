<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = ['table_number', 'capacity', 'qr_token', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getQrUrlAttribute(): string
    {
        return url('/table/' . $this->qr_token);
    }
}
