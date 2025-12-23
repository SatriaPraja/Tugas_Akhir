<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportLahan extends Command
{
    protected $signature = 'app:import-lahan';

    protected $description = 'Import data lahan dari CSV';

    public function handle()
    {
        $path = storage_path('app/dataset.csv');

        if (! file_exists($path)) {
            dd('FILE CSV TIDAK DITEMUKAN:', $path);
        }

        $csv = array_map('str_getcsv', file($path));
        $header = array_shift($csv);

        foreach ($csv as $row) {
            $data = array_combine($header, $row);

            // Pastikan semua key dari CSV dikonversi ke huruf kecil agar konsisten
            $data = array_change_key_case($data, CASE_LOWER);

            \App\Models\Lahan::updateOrCreate(
                ['nop' => $data['nop']], // Sisi kanan sudah huruf kecil
                [
                    'nama'           => $data['nama'],
                    'luas'           => $data['luas'],
                    'klaster'        => $data['klaster'],
                    'estimasi_panen' => $data['estimasi_panen'],
                    'produktivitas'  => $data['produktivitas'], // Tambah field Produktivitas
                    'jenis_tanah'    => $data['jenis_tanah'],
                    'lat'            => $this->fixCoord($data['lat']),
                    'lon'            => $this->fixCoord($data['lon']),
                ]
            );
        }

        return Command::SUCCESS;
    }

    public function fixCoord($val)
    {
        // Jika val sudah normal, langsung return
        if (is_numeric($val)) {
            return floatval($val);
        }

        // Jika format kacau: "10.906.860.458.092.200"
        $clean = str_replace('.', '', $val);

        if (strlen($clean) < 9) {
            return null;
        }

        $clean = substr($clean, 0, 3) . '.' . substr($clean, 3, 6);

        return floatval($clean);
    }
}
