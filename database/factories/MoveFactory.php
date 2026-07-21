<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Move;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Move>
 */
class MoveFactory extends Factory
{
    protected $model = Move::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'type' => fake()->randomElement(['fire', 'water', 'grass', 'electric', 'normal']),
            'power' => fake()->numberBetween(20, 120),
        ];
    }
}
