<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $fillable = [
        'cash_active', 'transfer_active', 'qris_active', 'qris_image_path',
    ];

    protected $casts = [
        'cash_active' => 'boolean',
        'transfer_active' => 'boolean',
        'qris_active' => 'boolean',
    ];

    public static function current(): self
    {
        return self::firstOrCreate([], [
            'cash_active' => true,
            'transfer_active' => true,
            'qris_active' => true,
        ]);
    }
}
