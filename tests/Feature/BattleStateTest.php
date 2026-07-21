<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Battle;
use App\Models\Move;
use App\Models\Pokemon;
use Database\Seeders\MoveSeeder;
use Database\Seeders\PokemonSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BattleStateTest extends TestCase
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

    public function test_can_start_a_new_battle(): void
    {
        $pikachu = Pokemon::where('name', 'Pikachu')->firstOrFail();
        $squirtle = Pokemon::where('name', 'Squirtle')->firstOrFail();

        $response = $this->postJson('/api/battles/start', [
            'pokemon_1_id' => $pikachu->id,
            'pokemon_2_id' => $squirtle->id,
        ]);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'Combate iniciado.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'pokemon_1_id',
                    'pokemon_2_id',
                    'pokemon_1_hp',
                    'pokemon_2_hp',
                    'current_turn_pokemon_id',
                    'status',
                ],
            ]);

        $this->assertDatabaseHas('battles', [
            'pokemon_1_id' => $pikachu->id,
            'pokemon_2_id' => $squirtle->id,
            'status'       => 'in_progress',
        ]);
    }

    public function test_executing_a_turn_reduces_defender_hp_and_switches_turn(): void
    {
        $pikachu = Pokemon::where('name', 'Pikachu')->firstOrFail();
        $squirtle = Pokemon::where('name', 'Squirtle')->firstOrFail();
        $move = Move::where('name', 'Thunderbolt')->firstOrFail();

        // Crear combate inicial con HP fija
        $battle = Battle::create([
            'pokemon_1_id'            => $pikachu->id,
            'pokemon_2_id'            => $squirtle->id,
            'pokemon_1_hp'            => 100,
            'pokemon_2_hp'            => 100,
            'current_turn_pokemon_id' => $pikachu->id, // Turno de Pikachu
            'status'                  => 'in_progress',
        ]);

        $response = $this->postJson("/api/battles/{$battle->id}/turn", [
            'move_id' => $move->id,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $battle->refresh();

        // Verificar que la vida del defensor (Squirtle) disminuyó
        $this->assertLessThan(100, $battle->pokemon_2_hp);
        // Verificar que el turno cambió a Squirtle
        $this->assertEquals($squirtle->id, $battle->current_turn_pokemon_id);
    }

    public function test_battle_finishes_when_hp_reaches_zero(): void
    {
        $pikachu = Pokemon::where('name', 'Pikachu')->firstOrFail();
        $squirtle = Pokemon::where('name', 'Squirtle')->firstOrFail();
        $move = Move::where('name', 'Thunderbolt')->firstOrFail();

        // Dejar a Squirtle a 1 PS para forzar su derrota en este turno
        $battle = Battle::create([
            'pokemon_1_id'            => $pikachu->id,
            'pokemon_2_id'            => $squirtle->id,
            'pokemon_1_hp'            => 100,
            'pokemon_2_hp'            => 1,
            'current_turn_pokemon_id' => $pikachu->id,
            'status'                  => 'in_progress',
        ]);

        $response = $this->postJson("/api/battles/{$battle->id}/turn", [
            'move_id' => $move->id,
        ]);

        $response->assertOk();

        $battle->refresh();

        // Verificar que los PS llegaron a 0, el combate acabó y hay un ganador (Parte 3 del PDF)
        $this->assertEquals(0, $battle->pokemon_2_hp);
        $this->assertEquals('finished', $battle->status);
        $this->assertEquals($pikachu->id, $battle->winner_id);
    }
}