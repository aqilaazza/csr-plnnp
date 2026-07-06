<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kelayakan', function (Blueprint $table) {
            $table->string('berkas_pdf')->nullable()->after('file_pdf');
        });
    }

    public function down(): void
    {
        Schema::table('kelayakan', function (Blueprint $table) {
            $table->dropColumn('berkas_pdf');
        });
    }
};