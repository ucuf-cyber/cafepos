<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class AdminBankAccountController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.settings.payment');
    }

    public function create() { return view('admin.settings.bank-create'); }

    public function store(Request $request)
    {
        BankAccount::create($this->validateData($request));
        return redirect()->route('admin.settings.payment')->with('success', 'Rekening ditambahkan.');
    }

    public function edit(int $id)
    {
        $bank = BankAccount::findOrFail($id);
        return view('admin.settings.bank-edit', compact('bank'));
    }

    public function update(Request $request, int $id)
    {
        BankAccount::findOrFail($id)->update($this->validateData($request));
        return redirect()->route('admin.settings.payment')->with('success', 'Rekening diperbarui.');
    }

    public function destroy(int $id)
    {
        BankAccount::findOrFail($id)->delete();
        return back()->with('success', 'Rekening dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'bank_name' => 'required|string|max:50',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]) + ['is_active' => $request->boolean('is_active', true)];
    }
}
