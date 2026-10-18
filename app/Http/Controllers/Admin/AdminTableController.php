<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdminTableController extends Controller
{
    public function index()
    {
        $tables = Table::orderBy('table_number')->get();
        return view('admin.tables.index', compact('tables'));
    }

    public function create() { return view('admin.tables.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'table_number' => 'required|string|max:20|unique:tables,table_number',
            'capacity' => 'required|integer|min:1|max:50',
            'is_active' => 'nullable|boolean',
        ]);
        $data['qr_token'] = Str::random(16);
        $data['is_active'] = $request->boolean('is_active', true);
        Table::create($data);
        return redirect()->route('admin.tables.index')->with('success', 'Meja ditambahkan.');
    }

    public function edit(int $id)
    {
        $table = Table::findOrFail($id);
        return view('admin.tables.edit', compact('table'));
    }

    public function update(Request $request, int $id)
    {
        $table = Table::findOrFail($id);
        $data = $request->validate([
            'table_number' => 'required|string|max:20|unique:tables,table_number,' . $id,
            'capacity' => 'required|integer|min:1|max:50',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $table->update($data);
        return redirect()->route('admin.tables.index')->with('success', 'Meja diperbarui.');
    }

    public function destroy(int $id)
    {
        Table::findOrFail($id)->delete();
        return back()->with('success', 'Meja dihapus.');
    }

    public function downloadQr(int $id)
    {
        $table = Table::findOrFail($id);
        $url = url('/table/' . $table->qr_token);
        $qr = QrCode::format('png')->size(500)->margin(2)->errorCorrection('M')->generate($url);
        return response($qr)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="QR-Meja-' . $table->table_number . '.png"');
    }

    public function printQr(int $id)
    {
        $table = Table::findOrFail($id);
        $url = url('/table/' . $table->qr_token);
        $qrSvg = QrCode::format('svg')->size(300)->margin(1)->errorCorrection('M')->generate($url);
        return view('admin.tables.print', compact('table', 'qrSvg', 'url'));
    }
}
