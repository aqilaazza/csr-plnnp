<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposal', function (Blueprint $table) {
            $table->foreignId('sub_instansi_id')
                ->nullable()
                ->after('kategori_instansi_id')
                ->constrained('sub_instansi')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('proposal', function (Blueprint $table) {
            $table->dropForeign(['sub_instansi_id']);
            $table->dropColumn('sub_instansi_id');
        });
    }
};