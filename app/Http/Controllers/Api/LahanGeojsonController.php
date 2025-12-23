<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lahan;

class LahanGeojsonController extends Controller
{
    public function index()
    {
        $features = [];

        foreach (Lahan::whereNotNull('polygon')->get() as $lahan) {

            $geometry = json_decode($lahan->polygon, true);

            // pastikan geometry valid
            if (! $geometry || ! isset($geometry['type'])) {
                continue;
            }

            $features[] = [
                'type' => 'Feature',
                'properties' => [
                    'id' => $lahan->id,
                   'nop' => $lahan->nop,
                'nama' => $lahan->nama,
                'luas' => $lahan->luas,
                'jenis_tanah' => $lahan->jenis_tanah,
                'estimasi_panen' => $lahan->estimasi_panen,
                'klaster' => $lahan->klaster,
                ],
                'geometry' => $geometry,
            ];
        }

        return [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
    }
}
