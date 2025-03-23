<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataTanah extends Model
{
    protected $fillable = [
        'nama_lokasi',
        'luas',
        'status_tanah',
        'keterangan',
        'dokumen',
    ];
}
