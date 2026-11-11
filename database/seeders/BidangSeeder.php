<?php

namespace Database\Seeders;

use App\Models\Bidang;
use Illuminate\Database\Seeder;

class BidangSeeder extends Seeder
{
    public function run(): void
    {
        $daftar = [
            'Koordinator',
            'Sekretariat',
            'Unit Registrasi dan Verifikasi',
            'Unit Administrasi sistem Elektronik',
            'Unit Layanan dan dukungan',
        ];

        foreach ($daftar as $nama) {
            Bidang::firstOrCreate(['nama' => $nama]);
        }
    }
}