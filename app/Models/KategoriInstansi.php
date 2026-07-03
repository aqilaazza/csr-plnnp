<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriInstansi extends Model
{
    protected $table = 'kategori_instansi';

    protected $fillable = ['nama'];

    public function proposal()
    {
        return $this->hasMany(Proposal::class, 'kategori_instansi_id');
    }
}