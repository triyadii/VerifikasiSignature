<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sertifikats', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_id_sertifikat', 200);
            $table->string('nama_pu', 200);
            $table->string('nama_usaha', 200);
            $table->text('alamat_lokasi_usaha');
            $table->date('tanggal_dikeluarkan_surat');
            $table->string('jenis_legalitas_usaha', 200);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sertifikats');
    }
};
