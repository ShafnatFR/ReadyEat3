<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@readyeat.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 2. Buat Akun Mahasiswa Dummy
        User::create([
            'name' => 'Shafnat Mahasiswa',
            'email' => 'shafnat@student.telkom.ac.id',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        // 3. Jalankan Seeder Menu (Data dari React)
        $this->call([
            MenuSeeder::class,
        ]);
    }
}