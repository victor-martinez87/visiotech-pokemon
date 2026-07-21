<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Pokemon;
use App\Models\UserPokemon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserPokemon>
 */
class UserPokemonFactory extends Factory
{
    protected $model = UserPokemon::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pokemon_id' => Pokemon::factory(),
            'current_hp' => fake()->numberBetween(20, 150),
        ];
    }
}
