<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\PaymentSetting;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function qrisImage()
    {
        $setting = PaymentSetting::current();
        if (!$setting->qris_image_path || !Storage::disk('public')->exists($setting->qris_image_path)) {
            abort(404, 'Gambar QRIS belum diatur.');
        }
        return response()->file(Storage::disk('public')->path($setting->qris_image_path));
    }

    public function bankAccounts()
    {
        return response()->json(
            BankAccount::where('is_active', true)->orderBy('sort_order')->get()
        );
    }
}
