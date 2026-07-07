<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_instansi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_instansi_id')
                ->constrained('kategori_instansi')
                ->onDelete('cascade');
            $table->string('nama');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_instansi');
    }
};