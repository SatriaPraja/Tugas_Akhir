<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; // Gunakan File untuk manajemen folder

class ImportLahanController extends Controller
{
    // Menampilkan daftar file CSV yang sudah diupload

    public function index()
    {
        $csvFiles = collect(glob(storage_path('app/private/csv/*.csv')))
            ->map(fn($f) => basename($f));

        // 4. Return View
        return view('admin.geojson.index', compact('csvFiles'));
    }

    // Mengupload file CSV ke storage
    public function upload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        // Simpan file
        $request->file('csv_file')->storeAs(
            'csv',
            $request->file('csv_file')->getClientOriginalName()
        );

        return back()->with('success', 'File CSV berhasil diupload!');
    }

    // Melakukan proses import data
    public function process($fileName)
    {
        $path = storage_path('app/private/csv/' . $fileName);

        if (!File::exists($path)) {
            return back()->with('error', 'File tidak ditemukan!');
        }

        try {
            $csvData = array_map('str_getcsv', file($path));
            $header = array_shift($csvData);
            $header = array_map('strtolower', $header);

            $count = 0;
            foreach ($csvData as $row) {
                if (empty($row[0])) continue;

                // Mencegah error jika jumlah kolom tidak sesuai header
                if (count($header) !== count($row)) continue;

                $data = array_combine($header, $row);
                $data = array_change_key_case($data, CASE_LOWER);

                Lahan::updateOrCreate(
                    ['nop' => $data['nop']],
                    [
                        'nama'           => $data['nama'] ?? '-',
                        'luas'           => $data['luas'] ?? 0,
                        'klaster'        => $data['klaster'] ?? 0,
                        'estimasi_panen' => $data['estimasi_panen'] ?? 0,
                        'produktivitas'  => $data['produktivitas'] ?? 0,
                        'jenis_tanah'    => $data['jenis_tanah'] ?? '-',
                        'lat'            => isset($data['lat']) ? $this->fixCoord($data['lat']) : null,
                        'lon'            => isset($data['lon']) ? $this->fixCoord($data['lon']) : null,
                    ]
                );
                $count++;
            }

            // Pastikan route ini sesuai dengan web.php Anda
            return redirect()->route('admin.import.index')->with('success', "Berhasil mengimport $count data dari $fileName");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses CSV: ' . $e->getMessage());
        }
    }

    // Tambahkan fungsi delete agar lengkap
    public function delete($fileName)
    {
        $path = storage_path('app/private/csv/' . $fileName);
        if (File::exists($path)) {
            File::delete($path);
            return back()->with('success', 'File berhasil dihapus!');
        }
        return back()->with('error', 'File tidak ditemukan!');
    }
    public function apiCekFiles()
    {
        $csvDir = storage_path('app/private/csv');

        // Cek apakah direktori ada
        $folderExists = File::exists($csvDir);

        // Ambil daftar file menggunakan glob
        $files = $folderExists ? glob($csvDir . '/*.csv') : [];

        // Sederhanakan nama file (ambil basename saja)
        $fileNames = array_map(function ($path) {
            return basename($path);
        }, $files);

        return response()->json([
            'status' => 'success',
            'folder_path' => $csvDir,
            'folder_exists' => $folderExists,
            'total_files' => count($fileNames),
            'files' => $fileNames,
            'permissions' => $folderExists ? substr(sprintf('%o', fileperms($csvDir)), -4) : null
        ]);
    }

    private function fixCoord($coord)
    {
        if (!$coord) return null;
        $clean = str_replace([' ', ','], ['', '.'], $coord);
        return is_numeric($clean) ? (float)$clean : null;
    }
}
