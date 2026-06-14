<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sertifikat extends Model
{
    protected $fillable = [
        'nomor_id_sertifikat',
        'nama_pu',
        'nama_usaha',
        'alamat_lokasi_usaha',
        'tanggal_dikeluarkan_surat',
        'jenis_legalitas_usaha',
        'signature',
        'public_key',
        'private_key',
        'pdf_path',
    ];
}
