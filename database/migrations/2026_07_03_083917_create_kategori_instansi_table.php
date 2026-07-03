<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_instansi', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });

        // Seed data lama supaya tidak hilang
        DB::table('kategori_instansi')->insert([
            ['nama' => 'Pemerintahan', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'APH (Polisi, Kejaksaan, Pengadilan)', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'TNI', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Lembaga Masyarakat', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_instansi');
    }
};