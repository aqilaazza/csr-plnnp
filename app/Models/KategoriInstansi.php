<?php
// app/Models/KategoriInstansi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriInstansi extends Model
{
    protected $table = 'kategori_instansi';

    protected $fillable = ['nama', 'contoh'];

    public function proposal()
    {
        return $this->hasMany(Proposal::class, 'kategori_instansi_id');
    }

    public function subInstansi()
    {
        return $this->hasMany(SubInstansi::class, 'kategori_instansi_id');
    }
}