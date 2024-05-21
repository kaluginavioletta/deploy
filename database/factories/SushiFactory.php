<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sushi>
 */
class SushiFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Sushi::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name_sushi' => $this->faker->word,
            'compound_sushi' => $this->faker->sentence,
            'id_view_sushi' => function () {
                return \App\Models\ViewSushi::factory()->create()->id_view_sushi;
            },
            'price_sushi' => $this->faker->randomFloat(2, 10, 50),
            'percent_discount_sushi' => $this->faker->numberBetween(0, 50),
            'discounted_price_sushi' => function ($attributes) {
                return $attributes['price_sushi'] * (1 - $attributes['percent_discount_sushi'] / 100);
            },
            'grams' => $this->faker->numberBetween(50, 500),
            'img_sushi' => $this->faker->imageUrl(640, 480),
        ];
    }
}
