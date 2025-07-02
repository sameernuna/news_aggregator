<?php

namespace Database\Factories;

use App\Models\Keyword;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Keyword>
 */
class KeywordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $keywords = [
            // Technology keywords
            'AI', 'Machine Learning', 'Blockchain', 'Cybersecurity', 'Cloud Computing', 'IoT', '5G', 'Virtual Reality', 'Augmented Reality', 'Robotics',
            // Business keywords
            'Startup', 'Investment', 'Market Analysis', 'Digital Transformation', 'E-commerce', 'Fintech', 'Cryptocurrency', 'Sustainability', 'Innovation', 'Strategy',
            // Politics keywords
            'Election', 'Policy', 'Government', 'International Relations', 'Diplomacy', 'Legislation', 'Democracy', 'Human Rights', 'Climate Policy', 'Trade',
            // Sports keywords
            'Football', 'Basketball', 'Tennis', 'Olympics', 'Championship', 'Athlete', 'Team', 'Tournament', 'Fitness', 'Training',
            // Entertainment keywords
            'Movie', 'Music', 'Celebrity', 'Streaming', 'Hollywood', 'Award', 'Concert', 'Film', 'Album', 'Performance',
            // Health keywords
            'Medical Research', 'Vaccine', 'Treatment', 'Wellness', 'Mental Health', 'Nutrition', 'Exercise', 'Healthcare', 'Pandemic', 'Therapy',
            // Science keywords
            'Research', 'Discovery', 'Space', 'Climate Change', 'Biology', 'Chemistry', 'Physics', 'Astronomy', 'Environment', 'Technology',
            // Education keywords
            'Learning', 'University', 'Student', 'Online Education', 'Curriculum', 'Teaching', 'Academic', 'Research', 'Innovation', 'Skills'
        ];

        return [
            'name' => fake()->unique()->randomElement($keywords),
        ];
    }

    /**
     * Create a technology keyword.
     */
    public function technology(): static
    {
        $techKeywords = ['AI', 'Machine Learning', 'Blockchain', 'Cybersecurity', 'Cloud Computing', 'IoT', '5G', 'Virtual Reality', 'Augmented Reality', 'Robotics'];
        
        return $this->state(fn (array $attributes) => [
            'name' => fake()->unique()->randomElement($techKeywords),
        ]);
    }

    /**
     * Create a business keyword.
     */
    public function business(): static
    {
        $businessKeywords = ['Startup', 'Investment', 'Market Analysis', 'Digital Transformation', 'E-commerce', 'Fintech', 'Cryptocurrency', 'Sustainability', 'Innovation', 'Strategy'];
        
        return $this->state(fn (array $attributes) => [
            'name' => fake()->unique()->randomElement($businessKeywords),
        ]);
    }

    /**
     * Create a sports keyword.
     */
    public function sports(): static
    {
        $sportsKeywords = ['Football', 'Basketball', 'Tennis', 'Olympics', 'Championship', 'Athlete', 'Team', 'Tournament', 'Fitness', 'Training'];
        
        return $this->state(fn (array $attributes) => [
            'name' => fake()->unique()->randomElement($sportsKeywords),
        ]);
    }
}
