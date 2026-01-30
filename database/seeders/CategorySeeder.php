<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['title' => 'Fiksi'],
            ['title' => 'Non Fiksi'],
            ['title' => 'Akademik'],
            ['title' => 'Komik'],
            ['title' => 'Novel'],
            ['title' => 'Dongeng'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
