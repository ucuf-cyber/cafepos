<?php

namespace Database\Seeders;

use App\Models\PaymentSetting;
use Illuminate\Database\Seeder;

class PaymentSettingSeeder extends Seeder
{
    public function run(): void
    {
        PaymentSetting::firstOrCreate([], [
            'cash_active' => true,
            'transfer_active' => true,
            'qris_active' => true,
        ]);
    }
}
