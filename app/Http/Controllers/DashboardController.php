<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lahan;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total petani = jumlah record lahan
        $totalPetani = Lahan::count();

        // Total luas lahan (sum)
        $totalLuas = Lahan::sum('luas');

        // Jumlah tiap klaster
        $klaster1 = Lahan::where('klaster', 1)->count();
        $klaster2 = Lahan::where('klaster', 2)->count();
        $klaster3 = Lahan::where('klaster', 3)->count();

        $avgPanen1 = \App\Models\Lahan::where('klaster', 1)->avg('estimasi_panen') ?? 0;
        $avgPanen2 = \App\Models\Lahan::where('klaster', 2)->avg('estimasi_panen') ?? 0;
        $avgPanen3 = \App\Models\Lahan::where('klaster', 3)->avg('estimasi_panen') ?? 0;

        $countKlaster1 = Lahan::where('klaster', 1)->count();
        $countKlaster2 = Lahan::where('klaster', 2)->count();
        $countKlaster3 = Lahan::where('klaster', 3)->count();

        // Distribusi terakhir (misal ambil 5 record terakhir berdasarkan created_at)
        $distribusiTerakhir = Lahan::orderBy('created_at', 'desc')
            ->take(5)
            ->get(['nama', 'nop', 'luas', 'jenis_tanah', 'klaster'])  // pilih kolom yang mau ditampilkan
            ->map(function ($item) {
                return [
                    'nama' => $item->nama,
                    'lokasi' => $item->nop,
                    'jenis' => $item->jenis_tanah,
                    'jumlah' => $item->luas,
                ];
            });

        return view('admin.dashboard', compact(
            'totalPetani',
            'totalLuas',
            'klaster1',
            'klaster2',
            'klaster3',
            'countKlaster1',
            'countKlaster2',
            'countKlaster3',
            'avgPanen1',
            'avgPanen2',
            'avgPanen3',
            'distribusiTerakhir'
        ));
    }
}
