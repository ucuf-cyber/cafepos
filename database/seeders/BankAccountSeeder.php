<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    public function run(): void
    {
        $banks = [
            ['bank_name' => 'BCA', 'account_number' => '1234567890', 'account_name' => 'CaféPos Owner', 'sort_order' => 1],
            ['bank_name' => 'Mandiri', 'account_number' => '0987654321', 'account_name' => 'CaféPos Owner', 'sort_order' => 2],
        ];
        foreach ($banks as $b) {
            BankAccount::updateOrCreate(
                ['bank_name' => $b['bank_name'], 'account_number' => $b['account_number']],
                $b + ['is_active' => true]
            );
        }
    }
}
