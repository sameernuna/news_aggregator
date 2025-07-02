<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authors = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@newsapi.com',
                'profile_image_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@guardian.com',
                'profile_image_url' => 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=200&h=200&fit=crop&crop=face',
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@nytimes.com',
                'profile_image_url' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&h=200&fit=crop&crop=face',
            ],
        ];

        foreach ($authors as $author) {
            Author::create($author);
        }
    }
} 