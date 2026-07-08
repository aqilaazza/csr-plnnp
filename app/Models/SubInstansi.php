<?php
// app/Models/SubInstansi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubInstansi extends Model
{
    protected $table = 'sub_instansi';

    protected $fillable = [
        'kategori_instansi_id',
        'nama',
    ];

    public function kategoriInstansi()
    {
        return $this->belongsTo(KategoriInstansi::class);
    }
}