<?php

namespace Database\Seeders;

use App\Models\NewsCategory;
use Illuminate\Database\Seeder;

class NewsCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Technology',
                'description' => 'Latest technology news, gadgets, and innovations',
            ],
            [
                'name' => 'Politics',
                'description' => 'Political news, government updates, and policy changes',
            ],
            [
                'name' => 'Business',
                'description' => 'Business news, market updates, and economic trends',
            ],
            [
                'name' => 'Sports',
                'description' => 'Sports news, match results, and athlete updates',
            ],
            [
                'name' => 'Entertainment',
                'description' => 'Entertainment news, movies, music, and celebrity updates',
            ],
            [
                'name' => 'Health',
                'description' => 'Health news, medical research, and wellness tips',
            ],
            [
                'name' => 'Science',
                'description' => 'Scientific discoveries, research findings, and space news',
            ],
            [
                'name' => 'Education',
                'description' => 'Education news, academic updates, and learning resources',
            ],
        ];

        foreach ($categories as $category) {
            NewsCategory::create($category);
        }
    }
} 