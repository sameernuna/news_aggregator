<?php

namespace Database\Seeders;

use App\Models\NewsArticle;
use App\Models\Publisher;
use App\Models\Author;
use App\Models\NewsCategory;
use App\Models\Keyword;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get category IDs by name
        $categories = NewsCategory::pluck('id', 'name')->toArray();

        $articles = [
            [
                'title' => 'Latest AI Breakthroughs in 2024',
                'content' => 'Artificial Intelligence has made significant strides in 2024, with new developments in machine learning, natural language processing, and computer vision. Companies are investing heavily in AI research, leading to breakthroughs in autonomous vehicles, healthcare diagnostics, and smart home technology. These advancements are reshaping industries and creating new opportunities for innovation.',
                'category_name' => 'Technology',
                'source_id' => 1, // NewsAPI
                'author_id' => 1, // John Smith
                'published_at' => now()->subDays(2),
                'keywords' => ['AI', 'Machine Learning', 'Technology', 'Innovation', 'Computer Vision']
            ],
            [
                'title' => 'Global Economic Trends and Market Analysis',
                'content' => 'The global economy is experiencing significant changes with emerging markets gaining prominence. Digital transformation is accelerating across industries, while traditional sectors are adapting to new technologies. Market analysts predict continued growth in technology stocks, with renewable energy and electric vehicles leading the charge.',
                'category_name' => 'Business',
                'source_id' => 2, // OpenNews
                'author_id' => 2, // Sarah Johnson
                'published_at' => now()->subDays(1),
                'keywords' => ['Market Analysis', 'Digital Transformation', 'Investment', 'Business', 'Economy']
            ],
            [
                'title' => 'Climate Change Policy Updates Worldwide',
                'content' => 'Governments around the world are implementing new policies to address climate change. The focus is on reducing carbon emissions, promoting renewable energy, and implementing sustainable practices. International agreements are being strengthened, with countries committing to ambitious targets for the next decade.',
                'category_name' => 'Politics',
                'source_id' => 3, // NewsCred
                'author_id' => 3, // Michael Brown
                'published_at' => now()->subHours(6),
                'keywords' => ['Climate Policy', 'Government', 'Policy', 'International Relations', 'Environment']
            ],
            [
                'title' => 'Major Sports Events and Championship Results',
                'content' => 'The sports world has been buzzing with exciting events and unexpected outcomes. Underdogs are rising to the occasion, while established champions are facing new challenges. The season has brought memorable moments and record-breaking performances across various sports.',
                'category_name' => 'Sports',
                'source_id' => 4, // The Guardian
                'author_id' => 1, // John Smith
                'published_at' => now()->subHours(3),
                'keywords' => ['Championship', 'Sports', 'Tournament', 'Athlete', 'Team']
            ],
            [
                'title' => 'Entertainment Industry Digital Transformation',
                'content' => 'The entertainment industry is undergoing a digital revolution with streaming services dominating the market. Traditional media companies are adapting to new consumption patterns, while independent creators are finding new platforms to reach audiences. The landscape is evolving rapidly with technology driving innovation.',
                'category_name' => 'Entertainment',
                'source_id' => 5, // New York Times
                'author_id' => 2, // Sarah Johnson
                'published_at' => now()->subHours(1),
                'keywords' => ['Streaming', 'Entertainment', 'Digital Transformation', 'Hollywood', 'Innovation']
            ],
            [
                'title' => 'Breakthrough Medical Research and Health Innovations',
                'content' => 'Medical researchers are making groundbreaking discoveries in various fields. New treatments for chronic diseases are being developed, while preventive medicine is gaining importance. Technology is playing a crucial role in healthcare delivery, with telemedicine and AI-assisted diagnostics becoming mainstream.',
                'category_name' => 'Health',
                'source_id' => 6, // BBC News
                'author_id' => 3, // Michael Brown
                'published_at' => now()->subMinutes(30),
                'keywords' => ['Medical Research', 'Healthcare', 'Treatment', 'Technology', 'Innovation']
            ],
            [
                'title' => 'Space Exploration and Scientific Discoveries',
                'content' => 'Space agencies worldwide are making remarkable progress in exploration and research. New planets are being discovered, while missions to Mars and beyond are advancing our understanding of the universe. Scientific breakthroughs in various fields are opening new possibilities for human advancement.',
                'category_name' => 'Science',
                'source_id' => 7, // NewsAPI.org
                'author_id' => 1, // John Smith
                'published_at' => now()->subMinutes(15),
                'keywords' => ['Space', 'Research', 'Discovery', 'Science', 'Astronomy']
            ],
            [
                'title' => 'Education Technology and Learning Innovations',
                'content' => 'The education sector is embracing technology to enhance learning experiences. Online platforms are becoming more sophisticated, while traditional institutions are integrating digital tools into their curricula. Personalized learning and adaptive technologies are transforming how students acquire knowledge.',
                'category_name' => 'Education',
                'source_id' => 1, // NewsAPI
                'author_id' => 2, // Sarah Johnson
                'published_at' => now()->subMinutes(5),
                'keywords' => ['Education', 'Learning', 'Technology', 'Online Education', 'Innovation']
            ],
        ];

        foreach ($articles as $articleData) {
            // Generate slug from title
            $slug = Str::slug($articleData['title']);
            $originalSlug = $slug;
            $counter = 1;

            // Ensure slug is unique
            while (NewsArticle::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Get category ID from name
            $categoryId = $categories[$articleData['category_name']] ?? null;

            if (!$categoryId) {
                // Create category if it doesn't exist
                $category = NewsCategory::create(['name' => $articleData['category_name']]);
                $categoryId = $category->id;
            }

            $article = NewsArticle::create([
                'title' => $articleData['title'],
                'slug' => $slug,
                'content' => $articleData['content'],
                'category_id' => $categoryId,
                'source_id' => $articleData['source_id'],
                'author_id' => $articleData['author_id'],
                'published_at' => $articleData['published_at'],
            ]);

            // Attach keywords to the article
            if (isset($articleData['keywords']) && is_array($articleData['keywords'])) {
                $keywordIds = [];
                foreach ($articleData['keywords'] as $keywordName) {
                    $keyword = Keyword::firstOrCreate(['name' => $keywordName]);
                    $keywordIds[] = $keyword->id;
                }
                $article->keywords()->attach($keywordIds);
            }
        }
    }
} 