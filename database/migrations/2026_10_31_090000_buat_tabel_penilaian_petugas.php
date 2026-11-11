<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('penilaian_petugas')) {
            return;
        }

        Schema::create('penilaian_petugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petugas_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('penilai_id')->constrained('users')->cascadeOnDelete();
            $table->string('periode', 7);              // contoh: 2026-10
            $table->unsignedTinyInteger('bintang');    // 1 sampai 5
            $table->string('catatan', 200)->nullable();
            $table->timestamps();

            $table->unique(['petugas_id', 'penilai_id', 'periode'], 'penilaian_petugas_unik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_petugas');
    }
};
