<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lahan;
use Illuminate\Http\Request;

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
        $request->validate([
            'geojson' => 'required|file|mimes:json,geojson',
        ]);

        $request->file('geojson')->storeAs(
            'private/geojson',
            $request->file('geojson')->getClientOriginalName()
        );

        return back()->with('success', 'File berhasil diupload!');
    }

    public function import($filePath = null)
    {
        $files = $filePath
            ? [storage_path('app/private/geojson/'.$filePath)]
            : glob(storage_path('app/private/geojson/*.geojson'));

        if (empty($files)) {
            return back()->with('error', 'Tidak ada file GeoJSON ditemukan!');
        }

        $inserted = 0;
        $updated = 0;
        $failed = [];

        foreach ($files as $path) {
            if (! file_exists($path)) {
                $failed[] = "File tidak ditemukan: $path";
                continue;
            }

            $content = file_get_contents($path);
            $geo = json_decode($content, true);

            if (! $geo) {
                $failed[] = 'Gagal membaca JSON: '.basename($path);
                continue;
            }

            if (! isset($geo['features'])) {
                $failed[] = 'Tidak ada features: '.basename($path);
                continue;
            }

            foreach ($geo['features'] as $f) {
                if (! isset($f['properties']['nop'])) {
                    $failed[] = 'NOP tidak ditemukan di file: '.basename($path);
                    continue;
                }

                $nop = trim($f['properties']['nop']);

                $lahan = Lahan::where('nop', $nop)->first();
                if (! $lahan) {
                    $failed[] = "NOP '$nop' tidak ditemukan di tabel lahan!";
                    continue;
                }

                if (! isset($f['geometry'])) {
                    $failed[] = "Geometry kosong untuk NOP '$nop'";
                    continue;
                }

                $polygon = json_encode($f['geometry']);
                $lahan->polygon = $polygon;
                $lahan->save();
                $updated++;
            }
        }

        return back()->with([
            'success' => "$updated polygon berhasil diupdate.",
            'error' => implode('<br>', $failed),
        ]);
    }

    public function importAll()
    {
        $this->import();
        return redirect()->back()->with('success', 'Semua GeoJSON berhasil diimport');
    }
}
