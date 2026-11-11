<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_beranda', function (Blueprint $table) {
            $table->id();
            $table->string('judul_utama')->nullable();
            $table->string('subjudul')->nullable();
            $table->string('judul_sambutan')->nullable();
            $table->text('teks_sambutan')->nullable();
            $table->text('daftar_layanan')->nullable();
            $table->text('daftar_langkah')->nullable();
            $table->string('teks_privasi')->nullable();
            $table->string('teks_footer')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('banner_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_beranda');
    }
};