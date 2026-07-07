<?php
// database/seeders/SubInstansiSeeder.php

namespace Database\Seeders;

use App\Models\KategoriInstansi;
use Illuminate\Database\Seeder;

class SubInstansiSeeder extends Seeder
{
    public function run(): void
    {
        $aph = KategoriInstansi::where('nama', 'APH (Polisi, Kejaksaan, Pengadilan)')->first();

        if ($aph) {
            $aph->subInstansi()->createMany([
                ['nama' => 'Pengadilan'],
                ['nama' => 'Kejaksaan'],
                ['nama' => 'Kepolisian'],
            ]);
        }
    }
}