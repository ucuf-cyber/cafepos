<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSettingController extends Controller
{
    public function payment()
    {
        $setting = PaymentSetting::current();
        $banks = BankAccount::orderBy('sort_order')->get();
        return view('admin.settings.payment', compact('setting', 'banks'));
    }

    public function updatePayment(Request $request)
    {
        $setting = PaymentSetting::current();
        $setting->update([
            'cash_active' => $request->boolean('cash_active'),
            'transfer_active' => $request->boolean('transfer_active'),
            'qris_active' => $request->boolean('qris_active'),
        ]);
        return back()->with('success', 'Pengaturan pembayaran diperbarui.');
    }

    public function updateQris(Request $request)
    {
        $request->validate([
            'qris_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);
        $setting = PaymentSetting::current();
        if ($setting->qris_image_path) {
            Storage::disk('public')->delete($setting->qris_image_path);
        }
        $path = $request->file('qris_image')->store('qris', 'public');
        $setting->update(['qris_image_path' => $path]);
        return back()->with('success', 'Gambar QRIS diperbarui.');
    }
}
