<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposal', function (Blueprint $table) {
            $table->dropColumn('kategori_instansi');
        });
    }

    public function down(): void
    {
        Schema::table('proposal', function (Blueprint $table) {
            $table->string('kategori_instansi')->nullable();
        });
    }
};