<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['type', 'title', 'message', 'data', 'is_read', 'target_role'];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];
}
