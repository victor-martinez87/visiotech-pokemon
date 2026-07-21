<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Pokemon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pokemon>
 */
class PokemonFactory extends Factory
{
    protected $model = Pokemon::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'type' => fake()->randomElement(['fire', 'water', 'grass', 'electric', 'normal']),
            'level' => fake()->numberBetween(1, 100),
            'hp' => fake()->numberBetween(20, 150),
            'attack' => fake()->numberBetween(20, 150),
            'defense' => fake()->numberBetween(20, 150),
            'sp_attack' => fake()->numberBetween(20, 150),
            'sp_defense' => fake()->numberBetween(20, 150),
            'speed' => fake()->numberBetween(20, 150),
        ];
    }
}
