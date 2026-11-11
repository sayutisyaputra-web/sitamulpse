<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kunjungans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kunjungan')->unique();

            // Data tamu
            $table->string('nama');
            $table->string('jenis_identitas')->default('KTP');
            $table->text('nomor_identitas');
            $table->string('nomor_identitas_hash', 64)->index();
            $table->string('instansi')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('no_hp', 20);
            $table->string('email')->nullable();

            // Data kunjungan
            $table->foreignId('bidang_id')->constrained('bidangs');
            $table->text('keperluan');
            $table->unsignedTinyInteger('jumlah_pendamping')->default(0);
            $table->string('status')->default('menunggu');
            $table->timestamp('waktu_masuk');
            $table->timestamp('waktu_keluar')->nullable();

            // Foto dan tanda tangan (disimpan alamat filenya)
            $table->string('foto_path');
            $table->string('tanda_tangan_path');

            // Persetujuan penggunaan data pribadi
            $table->boolean('setuju_data')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungans');
    }
};