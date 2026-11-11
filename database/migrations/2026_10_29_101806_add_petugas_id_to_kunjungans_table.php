<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('kunjungans', 'petugas_id')) {
            return;
        }

        Schema::table('kunjungans', function (Blueprint $table) {
            $table->foreignId('petugas_id')
                ->nullable()
                ->after('bidang_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->index(['petugas_id', 'status']);
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('kunjungans', 'petugas_id')) {
            return;
        }

        Schema::table('kunjungans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('petugas_id');
        });
    }
};