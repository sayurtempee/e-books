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

        // Sudah bisa forgot dan reset password langsung ke email active.

        User::factory()->create([
            'nik' => '3271051203980001',
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        User::factory()->create([
            'nik' => '3271051203980002',
            'name' => 'Seller Buku',
            'email' => 'seller@example.com',
            'password' => Hash::make('password'),
            'role' => 'seller'
        ]);

        User::factory()->create([
            'nik' => '3271051203980003',
            'name' => 'Buyer Buku',
            'email' => 'buyer@example.com',
            'password' => Hash::make('password'),
            'role' => 'buyer'
        ]);

        User::factory()->create([
            'nik' => '3271051203980004',
            'name' => 'Salma Aulia',
            'email' => 'moons@example.com',
            'password' => Hash::make('password'),
            'role' => 'buyer'
        ]);
    }
}
