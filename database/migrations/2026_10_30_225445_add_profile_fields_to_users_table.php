<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nik')) {
                $table->text('nik')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'nip')) {
                $table->string('nip', 18)->nullable()->unique()->after('nik');
            }
            if (!Schema::hasColumn('users', 'jabatan')) {
                $table->string('jabatan')->nullable()->after('nip');
            }
            if (!Schema::hasColumn('users', 'foto')) {
                $table->string('foto')->nullable()->after('jabatan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = array_filter(
                ['nik', 'nip', 'jabatan', 'foto'],
                fn ($col) => Schema::hasColumn('users', $col)
            );

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};