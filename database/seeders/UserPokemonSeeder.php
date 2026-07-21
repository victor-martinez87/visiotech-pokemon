<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Move;
use App\Models\Pokemon;
use App\Models\UserPokemon;
use Illuminate\Database\Seeder;

class UserPokemonSeeder extends Seeder
{
    public function run(): void
    {
        $pikachu = Pokemon::where('name', 'Pikachu')->first();
        $charmander = Pokemon::where('name', 'Charmander')->first();

        if ($pikachu) {
            $userPikachu = UserPokemon::firstOrCreate(
                ['pokemon_id' => $pikachu->id],
                ['current_hp' => $pikachu->hp]
            );

            $electricMove = Move::where('name', 'Thunderbolt')->first();
            $normalMove = Move::where('name', 'Quick Attack')->first();

            $userPikachu->moves()->syncWithoutDetaching(
                array_filter([$electricMove?->id, $normalMove?->id])
            );
        }

        if ($charmander) {
            $userCharmander = UserPokemon::firstOrCreate(
                ['pokemon_id' => $charmander->id],
                ['current_hp' => $charmander->hp]
            );

            $fireMove = Move::where('name', 'Flamethrower')->first();
            $tackleMove = Move::where('name', 'Tackle')->first();

            $userCharmander->moves()->syncWithoutDetaching(
                array_filter([$fireMove?->id, $tackleMove?->id])
            );
        }
    }
}