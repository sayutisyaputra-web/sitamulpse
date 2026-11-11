<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Penugasan petugas per unit layanan (kategori + sub layanan).
     *
     * Contoh baris: kategori "Sirup", sub "Fasmon 1", petugas X.
     * Untuk kategori tanpa pilihan lanjutan (misalnya "Sikap"), sub_layanan
     * diisi string kosong (bukan NULL) supaya aturan unik di bawah bekerja.
     *
     * Tabel bidang dan bidang_petugas TIDAK disentuh, jadi riwayat lama tetap utuh.
     */
    public function up(): void
    {
        Schema::create('layanan_petugas', function (Blueprint $table) {
            $table->id();
            $table->string('kategori_layanan', 50);
            $table->string('sub_layanan', 50)->default('');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['kategori_layanan', 'sub_layanan', 'user_id'], 'layanan_petugas_unik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_petugas');
    }
};
