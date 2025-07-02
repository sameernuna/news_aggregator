<?php

namespace Database\Factories;

use App\Models\UserPreference;
use App\Models\User;
use App\Models\NewsCategory;
use App\Models\Publisher;
use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserPreference>
 */
class UserPreferenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $preferenceTypes = ['category', 'source', 'author'];
        $preferenceType = fake()->randomElement($preferenceTypes);

        return [
            'user_id' => User::factory(),
            'preference_type' => $preferenceType,
            'preference_id' => $this->getPreferenceId($preferenceType),
        ];
    }

    /**
     * Create a category preference.
     */
    public function category(): static
    {
        return $this->state(fn (array $attributes) => [
            'preference_type' => 'category',
            'preference_id' => NewsCategory::inRandomOrder()->first()->id ?? NewsCategory::factory(),
        ]);
    }

    /**
     * Create a source preference.
     */
    public function source(): static
    {
        return $this->state(fn (array $attributes) => [
            'preference_type' => 'source',
            'preference_id' => Publisher::inRandomOrder()->first()->id ?? Publisher::factory(),
        ]);
    }

    /**
     * Create an author preference.
     */
    public function author(): static
    {
        return $this->state(fn (array $attributes) => [
            'preference_type' => 'author',
            'preference_id' => Author::inRandomOrder()->first()->id ?? Author::factory(),
        ]);
    }

    /**
     * Create preferences for a specific user.
     */
    public function forUser(int $userId): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
        ]);
    }

    /**
     * Get a random preference ID based on type.
     */
    private function getPreferenceId(string $type): int
    {
        switch ($type) {
            case 'category':
                return NewsCategory::inRandomOrder()->first()->id ?? NewsCategory::factory();
            case 'source':
                return Publisher::inRandomOrder()->first()->id ?? Publisher::factory();
            case 'author':
                return Author::inRandomOrder()->first()->id ?? Author::factory();
            default:
                return 1;
        }
    }
}
