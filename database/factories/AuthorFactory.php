<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Author>
 */
class AuthorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'profile_image_url' => fake()->imageUrl(200, 200, 'people'),
        ];
    }

    /**
     * Create an author with a specific name.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }

    /**
     * Create an author with a specific email.
     */
    public function withEmail(string $email): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => $email,
        ]);
    }

    /**
     * Create an author without profile image.
     */
    public function withoutProfileImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'profile_image_url' => null,
        ]);
    }

    /**
     * Create a journalist author.
     */
    public function journalist(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'profile_image_url' => fake()->imageUrl(200, 200, 'business'),
        ]);
    }
} 