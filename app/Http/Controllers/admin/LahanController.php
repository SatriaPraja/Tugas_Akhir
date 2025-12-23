<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lahan;

class LahanController extends Controller
{
    public function index(Request $request) // Tambahkan Request $request di sini
    {
        // 1. Inisialisasi query (Jangan pakai Lahan::all())
        $query = Lahan::query();

        // 2. Logika Pencarian (Nama atau NOP)
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                    ->orWhere('nop', 'like', "%{$request->search}%");
            });
        }

        // 3. Logika Filter Klaster
        if ($request->filled('filter_klaster')) {
            $query->where('klaster', $request->filter_klaster);
        }

        // 4. Ambil data hasil filter
        $lahan = $query->get();

        // 5. Baru return view di baris terakhir
        return view('admin.lahan.index', compact('lahan'));
    }
    public function update(Request $request, $id)
    {
        $lahan = Lahan::findOrFail($id);
        $lahan->update($request->only(['nama', 'nop', 'luas', 'jenis_tanah', 'estimasi_panen', 'klaster']));
        return redirect()->back()->with('success', 'Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        Lahan::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}
