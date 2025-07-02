<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Publisher>
 */
class PublisherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'description' => fake()->sentence(15),
        ];
    }

    /**
     * Create a publisher with a specific name.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }

    /**
     * Create a publisher without description.
     */
    public function withoutDescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => null,
        ]);
    }

    /**
     * Create a news publisher.
     */
    public function newsPublisher(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement([
                'CNN', 'BBC News', 'Reuters', 'Associated Press', 'The New York Times',
                'The Washington Post', 'USA Today', 'NPR', 'Al Jazeera', 'The Guardian'
            ]),
            'description' => fake()->sentence(10) . ' Leading news organization providing comprehensive coverage.',
        ]);
    }
} 