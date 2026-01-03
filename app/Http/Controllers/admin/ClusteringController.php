<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ClusteringController extends Controller
{
    public function index()
    {
        $totalLahan = Lahan::count();
        return view('admin.clustering.index', compact('totalLahan'));
    }

    public function process()
    {
        $lahan = Lahan::all();
        if ($lahan->isEmpty()) return back()->with('error', 'Data kosong.');

        // Siapkan data untuk dikirim ke Python
        $payload = $lahan->map(function ($item) {
            return [
                'luas' => (float) $item->luas,
                'produktivitas' => (float) $item->produktivitas,
                'jenis_tanah' => (int) $item->jenis_tanah,
            ];
        })->toArray();

        try {
            // Tembak API Flask
            $response = Http::timeout(30)->post('http://127.0.0.1:5000/predict', [
                'data' => $payload
            ]);

            if ($response->successful()) {
                $clusters = $response->json()['clusters'];

                // Pastikan jumlah hasil klaster sama dengan jumlah data lahan
                if (count($clusters) === $lahan->count()) {
                    foreach ($lahan as $index => $item) {
                        $item->update(['klaster' => $clusters[$index]]);
                    }
                    return redirect()->route('admin.lahan.index')->with('success', 'Klasterisasi AI Berhasil!');
                } else {
                    return back()->with('error', 'Jumlah hasil klaster tidak sinkron dengan data lahan.');
                }
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Pastikan Server Flask (app.py) sudah dijalankan!');
        }
    }
}
