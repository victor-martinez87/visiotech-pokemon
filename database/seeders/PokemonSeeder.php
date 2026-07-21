<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pokemon;

class PokemonSeeder extends Seeder
{
    public function run(): void
    {
        $pokemons = [
            [
                'name' => 'Pikachu',
                'type' => 'electric',
                'level' => 50,
                'hp' => 35,
                'attack' => 55,
                'defense' => 40,
                'sp_attack' => 50,
                'sp_defense' => 50,
                'speed' => 90,
            ],
            [
                'name' => 'Charmander',
                'type' => 'fire',
                'level' => 50,
                'hp' => 39,
                'attack' => 52,
                'defense' => 43,
                'sp_attack' => 60,
                'sp_defense' => 50,
                'speed' => 65,
            ],
            [
                'name' => 'Squirtle',
                'type' => 'water',
                'level' => 50,
                'hp' => 44,
                'attack' => 48,
                'defense' => 65,
                'sp_attack' => 50,
                'sp_defense' => 64,
                'speed' => 43,
            ],
            [
                'name' => 'Bulbasaur',
                'type' => 'grass',
                'level' => 50,
                'hp' => 45,
                'attack' => 49,
                'defense' => 49,
                'sp_attack' => 65,
                'sp_defense' => 65,
                'speed' => 45,
            ],
        ];

        foreach ($pokemons as $pokemon) {
            Pokemon::create($pokemon);
        }
    }
}