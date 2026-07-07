<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'proposal_id',
        'judul',
        'berkas',
        'deadline',
        'type',
        'is_read',
    ];

    protected $casts = [
        'deadline' => 'date',
        'is_read' => 'boolean',
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }
}
