<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeritaAcara extends Model
{
    protected $table = 'berita_acara';

    protected $fillable = [
        'proposal_id',
        'nomor_surat',
        'nama_penerima',
        'jabatan_penerima',
        'file_pdf',
        'file_upload',
        'bantuan',
        'business_support_id',
        'bisnis_support_lainnya',
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }
    public function businessSupport()
    {
        return $this->belongsTo(\App\Models\BusinessSupport::class, 'business_support_id');
    }
}
