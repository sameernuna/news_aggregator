<?php

namespace Database\Factories;

use App\Models\NewsArticle;
use App\Models\Publisher;
use App\Models\Author;
use App\Models\NewsCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NewsArticle>
 */
class NewsArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(6);
        $slug = Str::slug($title);
        
        return [
            'title' => $title,
            'slug' => $slug,
            'content' => fake()->paragraphs(3, true),
            'category_id' => NewsCategory::inRandomOrder()->first()->id ?? NewsCategory::factory(),
            'source_id' => Publisher::inRandomOrder()->first()->id ?? Publisher::factory(),
            'author_id' => Author::inRandomOrder()->first()->id ?? Author::factory(),
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Create an article with a specific title.
     */
    public function withTitle(string $title): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => $title,
            'slug' => Str::slug($title),
        ]);
    }

    /**
     * Create an article with a specific category.
     */
    public function withCategory(int $categoryId): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $categoryId,
        ]);
    }

    /**
     * Create a technology article.
     */
    public function technology(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => fake()->sentence(6) . ' - Tech News',
            'category_id' => NewsCategory::where('name', 'Technology')->first()->id ?? NewsCategory::factory(['name' => 'Technology']),
            'content' => fake()->paragraphs(4, true) . ' This article covers the latest developments in technology and innovation.',
        ]);
    }

    /**
     * Create a business article.
     */
    public function business(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => fake()->sentence(6) . ' - Business Update',
            'category_id' => NewsCategory::where('name', 'Business')->first()->id ?? NewsCategory::factory(['name' => 'Business']),
            'content' => fake()->paragraphs(4, true) . ' This article provides insights into current business trends and market analysis.',
        ]);
    }

    /**
     * Create a sports article.
     */
    public function sports(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => fake()->sentence(6) . ' - Sports News',
            'category_id' => NewsCategory::where('name', 'Sports')->first()->id ?? NewsCategory::factory(['name' => 'Sports']),
            'content' => fake()->paragraphs(4, true) . ' This article covers the latest sports events and athlete updates.',
        ]);
    }

    /**
     * Create an article with specific publisher and author.
     */
    public function withPublisherAndAuthor(int $publisherId, int $authorId): static
    {
        return $this->state(fn (array $attributes) => [
            'source_id' => $publisherId,
            'author_id' => $authorId,
        ]);
    }
} 