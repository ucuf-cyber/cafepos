<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'table_id', 'status', 'payment_method',
        'payment_status', 'customer_paid_claim', 'payment_confirmed_by',
        'payment_confirmed_at', 'cancellation_reason', 'notes', 'total_amount',
    ];

    protected $casts = [
        'payment_confirmed_at' => 'datetime',
        'customer_paid_claim' => 'boolean',
        'total_amount' => 'decimal:2',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'payment_confirmed_by');
    }

    public static function generateOrderNumber(): string
    {
        $last = self::orderByDesc('id')->first();
        $next = $last ? $last->id + 1 : 1;
        return '#' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
            'dikonfirmasi' => 'Dikonfirmasi',
            'diproses' => 'Diproses',
            'siap' => 'Siap',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ][$this->status] ?? $this->status;
    }
}
