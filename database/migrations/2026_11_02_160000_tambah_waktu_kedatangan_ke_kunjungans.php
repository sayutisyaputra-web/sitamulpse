<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambah kolom waktu_kedatangan (diisi manual oleh tamu di form).
     * Kolom waktu_masuk yang otomatis (dipakai dashboard, durasi, dan ekspor)
     * tidak disentuh sama sekali, jadi tidak ada yang bentrok.
     * Kolom ini boleh kosong, sehingga data lama tetap aman.
     */
    public function up(): void
    {
        Schema::table('kunjungans', function (Blueprint $table) {
            if (! Schema::hasColumn('kunjungans', 'waktu_kedatangan')) {
                $table->dateTime('waktu_kedatangan')->nullable()->after('waktu_masuk');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kunjungans', function (Blueprint $table) {
            if (Schema::hasColumn('kunjungans', 'waktu_kedatangan')) {
                $table->dropColumn('waktu_kedatangan');
            }
        });
    }
};
