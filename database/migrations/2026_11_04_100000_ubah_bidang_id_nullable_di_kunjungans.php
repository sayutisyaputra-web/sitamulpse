<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat kolom bidang_id di tabel kunjungans boleh kosong.
     *
     * Form tamu tidak lagi meminta bidang (digantikan layanan), jadi kunjungan baru
     * disimpan tanpa bidang. Data lama TIDAK berubah: bidang_id mereka tetap terisi,
     * dan tabel bidang beserta penugasannya tidak disentuh.
     */
    public function up(): void
    {
        Schema::table('kunjungans', function (Blueprint $table) {
            $table->unsignedBigInteger('bidang_id')->nullable()->change();
        });
    }

    /**
     * Mengembalikan menjadi wajib terisi hanya aman jika tidak ada kunjungan tanpa bidang,
     * jadi pengembalian ini sengaja tidak mengubah apa pun.
     */
    public function down(): void
    {
        //
    }
};
