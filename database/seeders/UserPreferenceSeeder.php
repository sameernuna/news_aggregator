<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserPreference;
use App\Models\NewsCategory;
use App\Models\Publisher;
use App\Models\Author;
use Illuminate\Database\Seeder;

class UserPreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users, categories, publishers, and authors
        $users = User::all();
        $categories = NewsCategory::all();
        $publishers = Publisher::all();
        $authors = Author::all();

        if ($users->isEmpty()) {
            // Create a default user if none exists
            $users = collect([User::factory()->create()]);
        }

        // Define preference patterns for different user types
        $preferencePatterns = [
            // Tech enthusiast user
            [
                'categories' => [1, 7], // Technology, Science
                'sources' => [1, 7], // NewsAPI, NewsAPI.org
                'authors' => [1], // John Smith
            ],
            // Business professional user
            [
                'categories' => [3, 8], // Business, Education
                'sources' => [2, 5], // OpenNews, New York Times
                'authors' => [2], // Sarah Johnson
            ],
            // Sports fan user
            [
                'categories' => [4], // Sports
                'sources' => [4], // The Guardian
                'authors' => [1, 3], // John Smith, Michael Brown
            ],
            // General news reader
            [
                'categories' => [2, 5, 6], // Politics, Entertainment, Health
                'sources' => [3, 6], // NewsCred, BBC News
                'authors' => [2, 3], // Sarah Johnson, Michael Brown
            ],
        ];

        foreach ($users as $index => $user) {
            // Select a preference pattern (cycle through patterns)
            $pattern = $preferencePatterns[$index % count($preferencePatterns)];

            // Add category preferences
            foreach ($pattern['categories'] as $categoryId) {
                if ($categories->contains('id', $categoryId)) {
                    UserPreference::firstOrCreate([
                        'user_id' => $user->id,
                        'preference_type' => 'category',
                        'preference_id' => $categoryId,
                    ]);
                }
            }

            // Add source preferences
            foreach ($pattern['sources'] as $sourceId) {
                if ($publishers->contains('id', $sourceId)) {
                    UserPreference::firstOrCreate([
                        'user_id' => $user->id,
                        'preference_type' => 'source',
                        'preference_id' => $sourceId,
                    ]);
                }
            }

            // Add author preferences
            foreach ($pattern['authors'] as $authorId) {
                if ($authors->contains('id', $authorId)) {
                    UserPreference::firstOrCreate([
                        'user_id' => $user->id,
                        'preference_type' => 'author',
                        'preference_id' => $authorId,
                    ]);
                }
            }

            // Add some random additional preferences for variety
            $this->addRandomPreferences($user, $categories, $publishers, $authors);
        }
    }

    /**
     * Add random preferences for variety.
     */
    private function addRandomPreferences($user, $categories, $publishers, $authors): void
    {
        // Add 1-2 random category preferences
        $randomCategories = $categories->random(min(2, $categories->count()));
        foreach ($randomCategories as $category) {
            UserPreference::firstOrCreate([
                'user_id' => $user->id,
                'preference_type' => 'category',
                'preference_id' => $category->id,
            ]);
        }

        // Add 1 random source preference
        $randomSource = $publishers->random();
        UserPreference::firstOrCreate([
            'user_id' => $user->id,
            'preference_type' => 'source',
            'preference_id' => $randomSource->id,
        ]);

        // Add 1 random author preference
        $randomAuthor = $authors->random();
        UserPreference::firstOrCreate([
            'user_id' => $user->id,
            'preference_type' => 'author',
            'preference_id' => $randomAuthor->id,
        ]);
    }
}
