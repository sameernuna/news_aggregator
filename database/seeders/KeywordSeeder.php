<?php

namespace Database\Seeders;

use App\Models\Keyword;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KeywordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $keywords = [
            // Technology keywords
            'AI', 'Machine Learning', 'Blockchain', 'Cybersecurity', 'Cloud Computing', 'IoT', '5G', 'Virtual Reality', 'Augmented Reality', 'Robotics', 'Data Science', 'Programming', 'Software Development', 'Mobile Apps', 'Web Development',
            
            // Business keywords
            'Startup', 'Investment', 'Market Analysis', 'Digital Transformation', 'E-commerce', 'Fintech', 'Cryptocurrency', 'Sustainability', 'Innovation', 'Strategy', 'Entrepreneurship', 'Venture Capital', 'Business Model', 'Revenue', 'Growth',
            
            // Politics keywords
            'Election', 'Policy', 'Government', 'International Relations', 'Diplomacy', 'Legislation', 'Democracy', 'Human Rights', 'Climate Policy', 'Trade', 'Foreign Policy', 'Political Campaign', 'Voting', 'Parliament', 'Constitution',
            
            // Sports keywords
            'Football', 'Basketball', 'Tennis', 'Olympics', 'Championship', 'Athlete', 'Team', 'Tournament', 'Fitness', 'Training', 'Soccer', 'Baseball', 'Golf', 'Swimming', 'Athletics',
            
            // Entertainment keywords
            'Movie', 'Music', 'Celebrity', 'Streaming', 'Hollywood', 'Award', 'Concert', 'Film', 'Album', 'Performance', 'Theater', 'Drama', 'Comedy', 'Documentary', 'Animation',
            
            // Health keywords
            'Medical Research', 'Vaccine', 'Treatment', 'Wellness', 'Mental Health', 'Nutrition', 'Exercise', 'Healthcare', 'Pandemic', 'Therapy', 'Medicine', 'Surgery', 'Prevention', 'Diagnosis', 'Recovery',
            
            // Science keywords
            'Research', 'Discovery', 'Space', 'Climate Change', 'Biology', 'Chemistry', 'Physics', 'Astronomy', 'Environment', 'Technology', 'Laboratory', 'Experiment', 'Theory', 'Hypothesis', 'Analysis',
            
            // Education keywords
            'Learning', 'University', 'Student', 'Online Education', 'Curriculum', 'Teaching', 'Academic', 'Research', 'Innovation', 'Skills', 'Course', 'Degree', 'Scholarship', 'Campus', 'Faculty'
        ];

        foreach ($keywords as $keyword) {
            Keyword::firstOrCreate(['name' => $keyword]);
        }
    }
}
