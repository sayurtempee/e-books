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
            'name' => 'Faris Hilmi Al - Iza',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'isOnline' => false,
        ]);

        // User::factory()->create([
        //     'nik' => '3271051203980002',
        //     'bank_name' => 'Mandiri',
        //     'no_rek' => '9000012345678', // Mandiri
        //     'name' => 'Bu Azizah',
        //     'email' => 'seller@example.com',
        //     'password' => Hash::make('password'),
        //     'role' => 'seller'
        // ]);

        // User::factory()->create([
        //     'nik' => '3271051203980003',
        //     'bank_name' => 'BRI',
        //     'no_rek' => '011101000123507', // BRI
        //     'name' => 'Bu Nanda',
        //     'email' => 'seller1@example.com',
        //     'password' => Hash::make('password'),
        //     'role' => 'seller',
        //     'isOnline' => false
        // ]);

        // User::factory()->create([
        //     'nik' => '3271051203980004',
        //     'bank_name' => 'BNI',
        //     'no_rek' => '6700012345', // BNI
        //     'name' => 'Salma Aulia',
        //     'email' => 'moons@example.com',
        //     'password' => Hash::make('password'),
        //     'role' => 'buyer',
        //     'isOnline' => false
        // ]);
    }
}

