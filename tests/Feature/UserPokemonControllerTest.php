<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Move;
use App\Models\Pokemon;
use App\Models\UserPokemon;
use Database\Seeders\MoveSeeder;
use Database\Seeders\PokemonSeeder;
use Database\Seeders\UserPokemonSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPokemonControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_user_pokemons_with_loaded_relations(): void
    {
        $this->seed([
            PokemonSeeder::class,
            MoveSeeder::class,
            UserPokemonSeeder::class,
        ]);

        $response = $this->getJson('/api/user-pokemons');

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'pokemon_id',
                        'current_hp',
                        'pokemon' => [
                            'id',
                            'name',
                            'type',
                        ],
                        'moves' => [
                            '*' => [
                                'id',
                                'name',
                                'type',
                                'power',
                            ],
                        ],
                    ],
                ],
            ]);

        $this->assertNotEmpty($response->json('data'));
        $this->assertNotEmpty($response->json('data.0.pokemon'));
        $this->assertNotEmpty($response->json('data.0.moves'));
    }

    public function test_store_creates_user_pokemon_with_up_to_four_moves(): void
    {
        $this->seed([
            PokemonSeeder::class,
            MoveSeeder::class,
        ]);

        $pokemon = Pokemon::where('name', 'Bulbasaur')->firstOrFail();
        $moveIds = Move::query()->limit(4)->pluck('id')->all();

        $response = $this->postJson('/api/user-pokemons', [
            'pokemon_id' => $pokemon->id,
            'move_ids' => $moveIds,
        ]);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath('data.pokemon_id', $pokemon->id)
            ->assertJsonPath('data.current_hp', $pokemon->hp)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'pokemon_id',
                    'current_hp',
                    'pokemon',
                    'moves',
                ],
            ]);

        $userPokemonId = $response->json('data.id');

        $this->assertDatabaseHas('user_pokemons', [
            'id' => $userPokemonId,
            'pokemon_id' => $pokemon->id,
            'current_hp' => $pokemon->hp,
        ]);

        $this->assertCount(4, $response->json('data.moves'));

        foreach ($moveIds as $moveId) {
            $this->assertDatabaseHas('user_pokemon_moves', [
                'user_pokemon_id' => $userPokemonId,
                'move_id' => $moveId,
            ]);
        }
    }

    public function test_destroy_removes_user_pokemon_and_returns_success_response(): void
    {
        $this->seed([
            PokemonSeeder::class,
            MoveSeeder::class,
        ]);

        $pokemon = Pokemon::where('name', 'Charmander')->firstOrFail();
        $move = Move::where('name', 'Flamethrower')->firstOrFail();

        $userPokemon = UserPokemon::create([
            'pokemon_id' => $pokemon->id,
            'current_hp' => $pokemon->hp,
        ]);

        $userPokemon->moves()->sync([$move->id]);

        $response = $this->deleteJson("/api/user-pokemons/{$userPokemon->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => null,
            ]);

        $this->assertDatabaseMissing('user_pokemons', [
            'id' => $userPokemon->id,
        ]);

        $this->assertDatabaseMissing('user_pokemon_moves', [
            'user_pokemon_id' => $userPokemon->id,
        ]);
    }
}
