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
        Schema::create('notifications', function (Blueprint $table) {

            $table->id();

            $table->foreignId('proposal_id')
                ->constrained('proposal')
                ->cascadeOnDelete();

            $table->string('judul');
            $table->string('berkas');

            $table->date('deadline');

            // jenis reminder
            $table->enum('type', [
                'today',
                'h1',
                'h2',
                'overdue',
                'other'
            ]);

            // sudah dibaca atau belum
            $table->boolean('is_read')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
