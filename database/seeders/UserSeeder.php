<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $akun = [
            ['name' => 'Administrator', 'email' => 'admin@bukutamu.test',    'role' => 'admin'],
            ['name' => 'Petugas Resepsionis', 'email' => 'petugas@bukutamu.test', 'role' => 'petugas'],
            ['name' => 'Pimpinan',      'email' => 'pimpinan@bukutamu.test', 'role' => 'pimpinan'],
        ];

        foreach ($akun as $data) {
            $user = User::firstOrNew(['email' => $data['email']]);

            $user->forceFill([
                'name'     => $data['name'],
                'role'     => $data['role'],
                'aktif'    => true,
                'password' => Hash::make('Ganti-Password-Ini-123'),
            ])->save();
        }
    }
}