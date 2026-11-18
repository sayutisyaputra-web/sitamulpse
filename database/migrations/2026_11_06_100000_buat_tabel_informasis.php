<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel informasi/berita yang tampil di halaman sambutan buku tamu.
     * Dikelola oleh admin dan superadmin. Tabel lain tidak disentuh.
     */
    public function up(): void
    {
        Schema::create('informasis', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 150);
            $table->text('isi');

            // Alamat berkas foto (disimpan di folder privat, ditampilkan lewat alamat khusus)
            $table->string('foto_path')->nullable();

            // Hanya informasi yang aktif yang tampil kepada tamu
            $table->boolean('aktif')->default(true);

            // Siapa yang membuat dan terakhir mengubah (kosong bila akunnya dihapus)
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('diubah_oleh')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informasis');
    }
};
