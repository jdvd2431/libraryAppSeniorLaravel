<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $faker = Faker::create();

        return [
            'title' => $this->faker->sentence,
            'author' => $this->faker->name,
            'description' => $this->faker->paragraph,
            'isbn' => $this->faker->unique()->isbn13,
            'pages' => $this->faker->numberBetween(100, 1000),
            'category' => $this->faker->word,
            'stock' => $this->faker->numberBetween(1, 20),
        ];
    }
}
