<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $kopi = Category::where('name', 'Kopi')->first();
        $minuman = Category::where('name', 'Minuman')->first();
        $dessert = Category::where('name', 'Dessert')->first();
        $makanan = Category::where('name', 'Makanan')->first();

        $items = [
            ['category_id' => $kopi->id, 'name' => 'Caramel Latte', 'description' => 'Espresso creamy dengan sirup karamel premium', 'price' => 45000, 'stock_qty' => 50, 'labels' => ['favorit'], 'variants' => [['name' => 'Ukuran', 'options' => ['S', 'M', 'L']], ['name' => 'Suhu', 'options' => ['Panas', 'Dingin']]]],
            ['category_id' => $kopi->id, 'name' => 'Americano', 'description' => 'Espresso klasik dengan air panas', 'price' => 28000, 'stock_qty' => 60, 'labels' => []],
            ['category_id' => $kopi->id, 'name' => 'Cappuccino', 'description' => 'Espresso dengan foam susu lembut', 'price' => 35000, 'stock_qty' => 40, 'labels' => ['favorit']],
            ['category_id' => $kopi->id, 'name' => 'Espresso Single', 'description' => 'Shot espresso single origin', 'price' => 22000, 'stock_qty' => 80, 'labels' => []],
            ['category_id' => $minuman->id, 'name' => 'Matcha Blend', 'description' => 'Matcha Jepang dengan susu segar', 'price' => 38000, 'stock_qty' => 30, 'labels' => ['baru']],
            ['category_id' => $minuman->id, 'name' => 'Es Coklat', 'description' => 'Cokelat dingin signature', 'price' => 32000, 'stock_qty' => 25, 'labels' => []],
            ['category_id' => $minuman->id, 'name' => 'Lemon Tea', 'description' => 'Teh lemon segar', 'price' => 25000, 'stock_qty' => 40, 'labels' => []],
            ['category_id' => $dessert->id, 'name' => 'Croissant Butter', 'description' => 'Croissant Prancis dengan butter premium', 'price' => 32000, 'stock_qty' => 3, 'labels' => ['favorit']],
            ['category_id' => $dessert->id, 'name' => 'Muffin Coklat', 'description' => 'Muffin coklat dengan chocolate chips', 'price' => 22000, 'stock_qty' => 8, 'labels' => []],
            ['category_id' => $dessert->id, 'name' => 'Tiramisu', 'description' => 'Tiramisu Italia klasik', 'price' => 42000, 'stock_qty' => 12, 'labels' => ['baru']],
            ['category_id' => $makanan->id, 'name' => 'Beef Burger', 'description' => 'Burger sapi dengan keju cheddar', 'price' => 55000, 'stock_qty' => 15, 'labels' => []],
            ['category_id' => $makanan->id, 'name' => 'Chicken Sandwich', 'description' => 'Sandwich ayam grill', 'price' => 48000, 'stock_qty' => 20, 'labels' => ['favorit']],
            ['category_id' => $makanan->id, 'name' => 'Pasta Carbonara', 'description' => 'Pasta krim dengan smoked beef', 'price' => 65000, 'stock_qty' => 18, 'labels' => []],
            ['category_id' => $makanan->id, 'name' => 'Nasi Goreng Spesial', 'description' => 'Nasi goreng dengan ayam & telur', 'price' => 45000, 'stock_qty' => 25, 'labels' => ['pedas']],
        ];

        foreach ($items as $i => $it) {
            MenuItem::updateOrCreate(
                ['name' => $it['name']],
                $it + ['stock_alert_min' => 10, 'is_available' => $it['stock_qty'] > 0, 'sort_order' => $i]
            );
        }
    }
}
