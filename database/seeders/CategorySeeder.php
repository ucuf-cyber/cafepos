<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $cats = [
            ['name' => 'Kopi', 'icon' => '☕', 'sort_order' => 1],
            ['name' => 'Minuman', 'icon' => '🥤', 'sort_order' => 2],
            ['name' => 'Dessert', 'icon' => '🍰', 'sort_order' => 3],
            ['name' => 'Makanan', 'icon' => '🍔', 'sort_order' => 4],
        ];
        foreach ($cats as $c) {
            Category::updateOrCreate(['name' => $c['name']], $c + ['is_active' => true]);
        }
    }
}
