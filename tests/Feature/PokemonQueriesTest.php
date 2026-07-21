<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Move;
use App\Models\Pokemon;
use Database\Seeders\MoveSeeder;
use Database\Seeders\PokemonSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PokemonQueriesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PokemonSeeder::class,
            MoveSeeder::class,
        ]);
    }

    public function test_get_pokemon_assigned_moves_returns_success_response(): void
    {
        $pokemon = Pokemon::firstOrFail();

        $response = $this->getJson("/api/pokemons/{$pokemon->id}/moves");

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'pokemon',
                    'moves',
                ],
            ]);
    }

    public function test_get_pokemon_possible_moves_filters_by_type_and_normal(): void
    {
        $pikachu = Pokemon::where('name', 'Pikachu')->firstOrFail();

        $response = $this->getJson("/api/pokemons/{$pikachu->id}/possible-moves");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'pokemon' => 'Pikachu',
                    'type'    => 'electric',
                ],
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'pokemon',
                    'type',
                    'possible_moves',
                ],
            ]);

        // Verificamos que los movimientos devueltos sean de tipo 'electric' o 'normal'
        $moves = $response->json('data.possible_moves');
        $this->assertNotEmpty($moves);

        foreach ($moves as $move) {
            $this->assertTrue(in_array($move['type'], ['electric', 'normal'], true));
        }
    }

    public function test_get_pokemons_by_move_returns_matching_pokemons(): void
    {
        $move = Move::where('name', 'Flamethrower')->firstOrFail();

        $response = $this->getJson("/api/moves/{$move->id}/pokemons");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'move' => 'Flamethrower',
                    'type' => 'fire',
                ],
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'move',
                    'type',
                    'pokemons',
                ],
            ]);

        $pokemons = $response->json('data.pokemons');
        $this->assertNotEmpty($pokemons);
    }

    public function test_queries_return_404_when_id_not_found(): void
    {
        $this->getJson('/api/pokemons/99999/moves')->assertNotFound();
        $this->getJson('/api/pokemons/99999/possible-moves')->assertNotFound();
        $this->getJson('/api/moves/99999/pokemons')->assertNotFound();
    }
}