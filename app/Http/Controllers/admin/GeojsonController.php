<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class GeojsonController extends Controller
{
    public function index()
    {
        $files = collect(glob(storage_path('app/private/geojson/*.geojson')))
            ->map(fn($f) => basename($f));

        return view('admin.geojson.index', compact('files'));
    }

    public function upload(Request $request)
    {
        // Validasi ekstensi geojson secara manual karena mime-type geojson sering terdeteksi sebagai json/text
        $request->validate([
            'geojson' => [
                'required',
                'file',
                'max:10240', // Maks 10MB
                function ($attribute, $value, $fail) {
                    $extension = strtolower($value->getClientOriginalExtension());
                    if ($extension !== 'geojson') {
                        $fail('Format file harus berupa .geojson');
                    }
                },
            ],
        ], [
            'geojson.required' => 'Silakan pilih file GeoJSON terlebih dahulu.',
            'geojson.file'     => 'Input harus berupa file.',
            'geojson.max'      => 'Ukuran file terlalu besar, maksimal adalah 10MB.',
        ]);

        if ($request->hasFile('geojson')) {
            $file = $request->file('geojson');
            $fileName = $file->getClientOriginalName();

            // Simpan ke folder 'private/geojson' agar konsisten dengan fungsi import Anda
            $file->storeAs('private/geojson', $fileName);

            return back()->with('success', "File $fileName berhasil diunggah!");
        }

        return back()->with('error', 'Gagal mengunggah file.');
    }

    public function import($filePath = null)
    {
        $files = $filePath
            ? [storage_path('app/private/geojson/' . $filePath)]
            : glob(storage_path('app/private/geojson/*.geojson'));

        if (empty($files)) {
            return back()->with('error', 'Tidak ada file GeoJSON yang ditemukan untuk diproses.');
        }

        $updated = 0;
        $failed = [];

        foreach ($files as $path) {
            if (!file_exists($path)) {
                $failed[] = "File tidak ditemukan: " . basename($path);
                continue;
            }

            $content = file_get_contents($path);
            $geo = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $failed[] = 'Format JSON rusak: ' . basename($path);
                continue;
            }

            if (!isset($geo['features'])) {
                $failed[] = 'Struktur GeoJSON tidak valid (Features tidak ditemukan): ' . basename($path);
                continue;
            }

            foreach ($geo['features'] as $f) {
                if (!isset($f['properties']['nop'])) {
                    $failed[] = 'Data NOP tidak ditemukan di properti fitur: ' . basename($path);
                    continue;
                }

                $nop = trim($f['properties']['nop']);
                $lahan = Lahan::where('nop', $nop)->first();

                if (!$lahan) {
                    $failed[] = "NOP '$nop' tidak ditemukan di database. Pastikan data lahan sudah di-import melalui CSV terlebih dahulu.";
                    continue;
                }

                if (!isset($f['geometry'])) {
                    $failed[] = "Geometri (Polygon) kosong untuk NOP '$nop'";
                    continue;
                }

                $lahan->polygon = json_encode($f['geometry']);
                $lahan->save();
                $updated++;
            }
        }

        // Jika ada yang berhasil, berikan sukses. Jika ada gagal, lampirkan pesan errornya.
        $status = back()->with('success', "Berhasil memperbarui $updated data polygon.");

        if (!empty($failed)) {
            $status->with('error', 'Beberapa data gagal diproses:<br>' . implode('<br>', array_unique($failed)));
        }

        return $status;
    }
    public function delete($fileName)
    {
        $path = storage_path('app/private/geojson/' . $fileName);
        if (File::exists($path)) {
            File::delete($path);
            return back()->with('success', 'File berhasil dihapus!');
        }
        return back()->with('error', 'File tidak ditemukan!');
    }

    public function importAll()
    {
        $this->import();
        return redirect()->back()->with('success', 'Semua GeoJSON berhasil diimport');
    }
}
