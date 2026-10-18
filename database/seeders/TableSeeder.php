<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            Table::updateOrCreate(
                ['table_number' => (string) $i],
                ['capacity' => $i <= 4 ? 2 : 4, 'qr_token' => Str::random(16), 'is_active' => true]
            );
        }
        Table::updateOrCreate(
            ['table_number' => 'VIP'],
            ['capacity' => 8, 'qr_token' => Str::random(16), 'is_active' => true]
        );
    }
}
