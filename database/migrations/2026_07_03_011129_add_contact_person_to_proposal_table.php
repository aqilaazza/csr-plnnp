<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposal', function (Blueprint $table) {
            $table->string('contact_person', 20)
                ->nullable()
                ->after('instansi_pengajuan');
        });
    }

    public function down(): void
    {
        Schema::table('proposal', function (Blueprint $table) {
            $table->dropColumn('contact_person');
        });
    }
};