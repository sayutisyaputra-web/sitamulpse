<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambah tiga kolom baru di tabel kunjungans.
     * Semua nullable, jadi data kunjungan lama tidak berubah dan tidak error.
     * Kolom "keperluan" tetap dipakai (hanya labelnya yang nanti diganti
     * menjadi "Permasalahan (Detail)").
     */
    public function up(): void
    {
        Schema::table('kunjungans', function (Blueprint $table) {
            if (! Schema::hasColumn('kunjungans', 'kategori_layanan')) {
                $table->string('kategori_layanan', 50)->nullable()->after('bidang_id');
            }

            if (! Schema::hasColumn('kunjungans', 'sub_layanan')) {
                $table->string('sub_layanan', 50)->nullable()->after('kategori_layanan');
            }

            if (! Schema::hasColumn('kunjungans', 'media_pelaporan')) {
                $table->string('media_pelaporan', 30)->nullable()->after('sub_layanan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kunjungans', function (Blueprint $table) {
            foreach (['media_pelaporan', 'sub_layanan', 'kategori_layanan'] as $kolom) {
                if (Schema::hasColumn('kunjungans', $kolom)) {
                    $table->dropColumn($kolom);
                }
            }
        });
    }
};
