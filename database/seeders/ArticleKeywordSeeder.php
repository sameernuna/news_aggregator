<?php

namespace Database\Seeders;

use App\Models\NewsArticle;
use App\Models\Keyword;
use Illuminate\Database\Seeder;

class ArticleKeywordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing article-keyword relationships first
        \DB::table('article_keyword')->truncate();

        // Get all articles and keywords
        $articles = NewsArticle::all();
        $keywords = Keyword::all();

        // Define keyword mappings for different categories
        $categoryKeywords = [
            'Technology' => ['AI', 'Machine Learning', 'Blockchain', 'Cybersecurity', 'Cloud Computing', 'IoT', '5G', 'Virtual Reality', 'Augmented Reality', 'Robotics', 'Data Science', 'Programming', 'Software Development'],
            'Business' => ['Startup', 'Investment', 'Market Analysis', 'Digital Transformation', 'E-commerce', 'Fintech', 'Cryptocurrency', 'Sustainability', 'Innovation', 'Strategy', 'Entrepreneurship', 'Venture Capital'],
            'Politics' => ['Election', 'Policy', 'Government', 'International Relations', 'Diplomacy', 'Legislation', 'Democracy', 'Human Rights', 'Climate Policy', 'Trade', 'Foreign Policy'],
            'Sports' => ['Football', 'Basketball', 'Tennis', 'Olympics', 'Championship', 'Athlete', 'Team', 'Tournament', 'Fitness', 'Training', 'Soccer', 'Baseball'],
            'Entertainment' => ['Movie', 'Music', 'Celebrity', 'Streaming', 'Hollywood', 'Award', 'Concert', 'Film', 'Album', 'Performance', 'Theater', 'Drama'],
            'Health' => ['Medical Research', 'Vaccine', 'Treatment', 'Wellness', 'Mental Health', 'Nutrition', 'Exercise', 'Healthcare', 'Pandemic', 'Therapy', 'Medicine'],
            'Science' => ['Research', 'Discovery', 'Space', 'Climate Change', 'Biology', 'Chemistry', 'Physics', 'Astronomy', 'Environment', 'Technology', 'Laboratory'],
            'Education' => ['Learning', 'University', 'Student', 'Online Education', 'Curriculum', 'Teaching', 'Academic', 'Research', 'Innovation', 'Skills', 'Course']
        ];

        foreach ($articles as $article) {
            // Get the category name for this article
            $categoryName = $article->newsCategory->name;
            
            // Get relevant keywords for this category
            $relevantKeywords = $categoryKeywords[$categoryName] ?? [];
            
            // Get keyword IDs for relevant keywords
            $keywordIds = Keyword::whereIn('name', $relevantKeywords)->pluck('id')->toArray();
            
            // Add 2-4 random keywords from the relevant category
            $selectedKeywords = collect($keywordIds)->random(min(rand(2, 4), count($keywordIds)));
            
            // Also add 1-2 random keywords from other categories for variety
            $otherKeywords = Keyword::whereNotIn('name', $relevantKeywords)->pluck('id')->toArray();
            if (!empty($otherKeywords)) {
                $additionalKeywords = collect($otherKeywords)->random(min(rand(1, 2), count($otherKeywords)));
                $selectedKeywords = $selectedKeywords->merge($additionalKeywords);
            }
            
            // Attach keywords to the article using syncWithoutDetaching to prevent duplicates
            $article->keywords()->syncWithoutDetaching($selectedKeywords->toArray());
        }
    }
}
