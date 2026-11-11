<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('galeri_beranda', function (Blueprint $table) {
            $table->id();
            $table->string('keterangan', 200)->nullable();
            $table->string('foto_path');
            $table->unsignedInteger('urutan')->default(0);
            $table->boolean('tampil')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galeri_beranda');
    }
};