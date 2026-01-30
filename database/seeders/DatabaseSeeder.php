<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Bank Name = 'BCA', 'Mandiri', 'BNI', 'BRI'

        // Report
        // Benerin di laporan nya agar ada filter bulan dan tahun, lalu untuk grafiknya ada 2 yaitu total omzet nya berapa dan capital nya berapa.

        // Sudah bisa forgot dan reset password langsung ke email active.

        User::factory()->create([
            'nik' => '3271051203980001',
            'bank_name' => 'BCA',
            'no_rek' => '1234567890', // BCA
            'name' => 'Admin Book',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'isOnline' => false,
            'address' => 'Ky Tinggi',
        ]);
    }
}

