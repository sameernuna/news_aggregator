<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone_no' => '+1234567890',
        ]);

        // Seed news categories, publishers, authors, and articles
        $this->call([
            NewsCategorySeeder::class,
            PublisherSeeder::class,
            AuthorSeeder::class,
            NewsArticleSeeder::class,
            KeywordSeeder::class,
            ArticleKeywordSeeder::class,
            UserPreferenceSeeder::class,
        ]);
    }
}
