<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('berita_acara', function (Blueprint $table) {
            // nullable karena bisa diisi salah satu: pilih dari master, atau manual
            $table->unsignedBigInteger('business_support_id')->nullable()->after('proposal_id');
            $table->string('bisnis_support_lainnya')->nullable()->after('business_support_id');

            // Aktifkan ini kalau nama tabel business support-nya sudah pasti,
            // misal 'business_supports':
            // $table->foreign('business_support_id')->references('id')->on('business_supports')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('berita_acara', function (Blueprint $table) {
            $table->dropColumn(['business_support_id', 'bisnis_support_lainnya']);
        });
    }
};