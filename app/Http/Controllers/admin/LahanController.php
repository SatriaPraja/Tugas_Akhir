<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lahan;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $urea = round($luasHa * 275, 0);
        $npk  = round($luasHa * 250, 0);

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
        // 1. Cari data atau gagalkan jika tidak ada
        $lahan = Lahan::findOrFail($id);

        // 2. Validasi Input (Ini kunci agar tidak bisa simpan data kosong/spasi)
        $validatedData = $request->validate([
            'nama'           => 'required|string|max:255',
            'nop'            => 'required|string|max:50',
            'luas'           => 'required|numeric|min:1',
            'jenis_tanah'    => 'required|string',
            'estimasi_panen' => 'required|numeric|min:0',
            'urea'           => 'nullable|numeric|min:0',
            'npk'            => 'nullable|numeric|min:0',
            'klaster'        => 'required',
        ], [
            // Custom pesan error jika diperlukan
            'required' => ':attribute tidak boleh kosong atau hanya berisi spasi.',
            'numeric'  => ':attribute harus berupa angka.',
        ]);

        // 3. Update menggunakan data yang sudah divalidasi
        $lahan->update($validatedData);

        return redirect()->back()->with('success', 'Data lahan dan pupuk berhasil diperbarui');
    }

    public function destroy($id)
    {
        Lahan::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'new Data berhasil dihapus');
    }


    public function exportPdf()
    {
        // 1. Ambil data
        $lahan = Lahan::all();

        // 2. Buat string HTML langsung di sini
        $html = '
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
    </style>
    <h2>Laporan Data Lahan</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>NOP</th>
                <th>Nama</th>
                <th>Luas</th>
                <th>Klaster</th>
                <th>Urea</th>
                <th>NPK</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($lahan as $item) {
            $html .= '
            <tr>
                <td>' . $item->id . '</td>
                <td>' . $item->nop . '</td>
                <td>' . $item->nama . '</td>
                <td>' . number_format($item->luas) . ' m²</td>
                <td>' . ($item->klaster ?? '-') . '</td>
                <td>' . $item->urea . ' kg</td>
                <td>' . $item->npk . ' kg</td>
            </tr>';
        }

        $html .= '</tbody></table>';

        // 3. Load HTML string ke PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);

        // Set kertas ke landscape agar muat banyak kolom
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Lahan_Direct.pdf');
    }
    public function exportCsv()
    {
        $fileName = 'data_lahan_lengkap_' . date('Y-m-d') . '.csv';
        $lahan = Lahan::all();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        // 1. Definisikan Header sesuai fillable (tanpa polygon)
        $columns = [
            'id',
            'nop',
            'nama',
            'luas',
            'klaster',
            'estimasi_panen',
            'produktivitas',
            'urea',
            'npk',
            'jenis_tanah',
            'lat',
            'lon'
        ];

        $callback = function () use ($lahan, $columns) {
            $file = fopen('php://output', 'w');

            // Tambahkan BOM agar karakter khusus terbaca dengan benar di Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, $columns);

            foreach ($lahan as $item) {
                // 2. Masukkan semua data sesuai urutan header di atas
                fputcsv($file, [
                    $item->id,
                    $item->nop,
                    $item->nama,
                    $item->luas,
                    $item->klaster,
                    $item->estimasi_panen,
                    $item->produktivitas,
                    $item->urea,
                    $item->npk,
                    $item->jenis_tanah,
                    $item->lat, // pastikan nama kolom di DB 'lat' atau 'latitude'
                    $item->lon  // pastikan nama kolom di DB 'lon' atau 'longitude'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
