<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration.
     *
     * 1. Tambah kolom baru: jenis_bantuan, nominal, jumlah_barang, satuan.
     * 2. Migrasi data lama dari kolom `bantuan` (JSON) ke kolom-kolom baru.
     *    Jika satu baris punya beberapa item bantuan, nilainya digabung
     *    dengan koma, urutannya sejajar dengan jenis_bantuan.
     */
    public function up(): void
    {
        Schema::table('berita_acara', function (Blueprint $table) {
            $table->text('jenis_bantuan')->nullable()->after('bantuan');
            $table->text('nominal')->nullable()->after('jenis_bantuan');
            $table->text('jumlah_barang')->nullable()->after('nominal');
            $table->text('satuan')->nullable()->after('jumlah_barang');
        });

        DB::table('berita_acara')
            ->whereNotNull('bantuan')
            ->orderBy('id')
            ->chunkById(100, function ($rows) {
                foreach ($rows as $row) {
                    $decoded = json_decode($row->bantuan, true);

                    if (!is_array($decoded) || !isset($decoded['jenis'], $decoded['jumlah'])) {
                        continue;
                    }

                    $jenisArr  = (array) $decoded['jenis'];
                    $jumlahArr = (array) $decoded['jumlah'];

                    $jenisOut   = [];
                    $nominalOut = [];
                    $barangOut  = [];
                    $satuanOut  = [];

                    foreach ($jenisArr as $i => $jenis) {
                        $mentah = (string) ($jumlahArr[$i] ?? '');
                        [$nominal, $jumlahBarang, $satuan] = self::parseJumlah($mentah);

                        $jenisOut[]   = $jenis;
                        $nominalOut[] = $nominal ?? '-';
                        $barangOut[]  = $jumlahBarang ?? '-';
                        $satuanOut[]  = $satuan ?? '-';
                    }

                    DB::table('berita_acara')
                        ->where('id', $row->id)
                        ->update([
                            'jenis_bantuan' => implode(', ', $jenisOut),
                            'nominal'       => implode(', ', $nominalOut),
                            'jumlah_barang' => implode(', ', $barangOut),
                            'satuan'        => implode(', ', $satuanOut),
                        ]);
                }
            });
    }

    /**
     * Balikkan migration (hanya struktur kolom, data hasil parsing tidak
     * dikembalikan karena sumber aslinya masih ada di kolom `bantuan`).
     */
    public function down(): void
    {
        Schema::table('berita_acara', function (Blueprint $table) {
            $table->dropColumn(['jenis_bantuan', 'nominal', 'jumlah_barang', 'satuan']);
        });
    }

    /**
     * Pecah 1 nilai mentah dari array "jumlah" menjadi:
     * [nominal, jumlah_barang, satuan]
     *
     * Aturan:
     * - Diawali "Rp" (dengan/tanpa titik/spasi)  -> dianggap nominal (uang).
     * - Angka murni tanpa teks                   -> dianggap nominal (uang).
     * - Pola "angka + teks" (mis. "500 bibit")    -> jumlah_barang + satuan.
     * - Selain itu (tidak cocok pola)             -> ditaruh mentah di kolom satuan
     *   sebagai fallback, supaya datanya tidak hilang dan bisa dicek manual.
     */
    private static function parseJumlah(string $mentah): array
    {
        $mentah = trim($mentah);

        if ($mentah === '') {
            return [null, null, null];
        }

        // Format uang, misal "Rp3.000.000", "Rp 20.000.000", "Rp2.000.000,-"
        if (preg_match('/^Rp\.?\s*([\d.,]+)/i', $mentah, $m)) {
            $angka = rtrim($m[1], ',.-');
            $angka = str_replace(['.', ','], '', $angka); // hilangkan pemisah ribuan
            return [$angka, null, null];
        }

        // Angka murni tanpa satuan, misal "3000000" -> dianggap nominal uang
        if (preg_match('/^[\d.,]+$/', $mentah)) {
            $angka = str_replace(['.', ','], '', $mentah);
            return [$angka, null, null];
        }

        // Pola "angka satuan", misal "500 bibit", "1 unit", "2 ton", "100 Paket Sembako"
        if (preg_match('/^([\d.,]+)\s+(.+)$/', $mentah, $m)) {
            return [null, $m[1], trim($m[2])];
        }

        // Tidak cocok pola apapun -> simpan mentah di kolom satuan sebagai fallback
        return [null, null, $mentah];
    }
};