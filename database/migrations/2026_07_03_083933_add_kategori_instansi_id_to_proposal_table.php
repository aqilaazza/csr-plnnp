<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposal', function (Blueprint $table) {
            $table->foreignId('kategori_instansi_id')
                ->nullable()
                ->after('kategori_instansi')
                ->constrained('kategori_instansi')
                ->nullOnDelete();
        });

        // Migrasi otomatis data lama (string) -> id baru
        // Kalau ada value lama yang berbeda persis dengan seed di atas, mapping ini akan jalan otomatis.
        DB::statement("
            UPDATE proposal p
            JOIN kategori_instansi k ON p.kategori_instansi = k.nama
            SET p.kategori_instansi_id = k.id
        ");

        // Mapping tambahan untuk value lama 'APH' (tanpa keterangan lengkap)
        DB::statement("
            UPDATE proposal p
            JOIN kategori_instansi k ON k.nama = 'APH (Polisi, Kejaksaan, Pengadilan)'
            SET p.kategori_instansi_id = k.id
            WHERE p.kategori_instansi = 'APH'
        ");
    }

    public function down(): void
    {
        Schema::table('proposal', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kategori_instansi_id');
        });
    }
};