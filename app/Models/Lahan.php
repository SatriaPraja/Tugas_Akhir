<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lahan extends Model
{
    protected $table = 'lahan';

    protected $fillable = [
        'id','nop', 'nama', 'luas', 'klaster', 'estimasi_panen', 'produktivitas',
        'jenis_tanah', 'lat', 'lon', 'polygon',
    ];
}
