<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationLog extends Model
{
    protected $fillable = [
        'user_id',
        'filename',
        'status',
        'nama_penandatangan',
        'instansi',
        'waktu_ttd',
        'ip_address',
        'keterangan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
