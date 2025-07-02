<?php

namespace Database\Seeders;

use App\Models\Publisher;
use Illuminate\Database\Seeder;

class PublisherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $publishers = [
            [
                'name' => 'NewsAPI',
                'description' => 'Leading news aggregation service providing access to thousands of news sources worldwide.',
            ],
            [
                'name' => 'OpenNews',
                'description' => 'Open-source news platform providing real-time news updates and comprehensive coverage.',
            ],
            [
                'name' => 'NewsCred',
                'description' => 'Content marketing platform offering curated news content and brand journalism solutions.',
            ],
            [
                'name' => 'The Guardian',
                'description' => 'British daily newspaper known for investigative journalism, liberal editorial stance, and international coverage.',
            ],
            [
                'name' => 'New York Times',
                'description' => 'Prestigious American newspaper known for in-depth reporting, analysis, and investigative journalism.',
            ],
            [
                'name' => 'BBC News',
                'description' => 'British Broadcasting Corporation - Trusted source for international news, analysis, and investigative journalism.',
            ],
            [
                'name' => 'NewsAPI.org',
                'description' => 'Comprehensive news API service providing access to breaking news and articles from major publishers.',
            ],
        ];

        foreach ($publishers as $publisher) {
            Publisher::create($publisher);
        }
    }
} 