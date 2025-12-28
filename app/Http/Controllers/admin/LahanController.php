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
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'nop'            => 'required|string|unique:lahan,nop',
            'nama'           => 'required|string|max:255',
            'luas'           => 'required|numeric|min:1',
            'estimasi_panen' => 'required|numeric|min:0',
            'jenis_tanah'    => 'required|integer',
        ]);

        // 2. Hitung Luas dalam Hektar (1 Ha = 10.000 m2)
        $luasHa = $request->luas / 10000;

        // 3. Hitung Otomatis Pupuk (Dosis: Urea 275kg/ha & NPK 250kg/ha)
        $urea = round($luasHa * 275, 2);
        $npk  = round($luasHa * 250, 2);

        // 4. Hitung Otomatis Produktivitas (Hasil / Luas Ha)
        $produktivitas = $luasHa > 0 ? round($request->estimasi_panen / $luasHa) : 0;

        // 5. Simpan ke Database
        Lahan::create([
            'nop'            => $request->nop,
            'nama'           => $request->nama,
            'luas'           => $request->luas,
            'jenis_tanah'    => $request->jenis_tanah, // Disimpan sebagai integer
            'estimasi_panen' => $request->estimasi_panen,
            'produktivitas'  => $produktivitas,
            'urea'           => $urea,
            'npk'            => $npk,

            // Kosongkan field spasial & klaster sesuai permintaan
            'klaster'        => null,
            'longitude'      => null,
            'latitude'       => null,
            'polygon'        => null,
        ]);

        return redirect()->back()->with('success', 'Data lahan berhasil ditambahkan!');
    }
    public function update(Request $request, $id)
    {
        $lahan = Lahan::findOrFail($id);

        // Tambahkan 'urea' dan 'npk' ke dalam array only()
        $lahan->update($request->only([
            'nama',
            'nop',
            'luas',
            'jenis_tanah',
            'estimasi_panen',
            'produktivitas', // Tambahkan ini jika di view ada inputnya
            'klaster',
            'urea', // Kolom baru
            'npk'   // Kolom baru
        ]));

        return redirect()->back()->with('success', 'new Data lahan dan pupuk berhasil diperbarui');
    }

    public function destroy($id)
    {
        Lahan::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}
