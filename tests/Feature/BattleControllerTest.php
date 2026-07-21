<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Move;
use App\Models\Pokemon;
use Database\Seeders\MoveSeeder;
use Database\Seeders\PokemonSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BattleControllerTest extends TestCase
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

    public function test_calculate_damage_returns_expected_json_structure_with_valid_payload(): void
    {
        $attacker = Pokemon::where('name', 'Pikachu')->firstOrFail();
        $defender = Pokemon::where('name', 'Squirtle')->firstOrFail();
        $move = Move::where('name', 'Thunderbolt')->firstOrFail();

        $response = $this->postJson('/api/battle/calculate', [
            'attacker_id' => $attacker->id,
            'defender_id' => $defender->id,
            'move_id' => $move->id,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'attacker' => ['id', 'name', 'type', 'level'],
                    'defender' => ['id', 'name', 'type', 'level'],
                    'move' => ['id', 'name', 'type', 'power'],
                    'calculation' => [
                        'damage',
                        'effectiveness',
                        'is_special',
                        'message',
                    ],
                ],
            ])
            ->assertJsonPath('data.calculation.damage', 66)
            ->assertJsonPath('data.calculation.effectiveness', 2);
    }

    public function test_calculate_damage_returns_validation_errors_when_parameters_are_missing(): void
    {
        $response = $this->postJson('/api/battle/calculate', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'attacker_id',
                'defender_id',
                'move_id',
            ]);
    }

    public function test_calculate_damage_returns_validation_errors_when_parameters_are_invalid(): void
    {
        $response = $this->postJson('/api/battle/calculate', [
            'attacker_id' => 99999,
            'defender_id' => 99998,
            'move_id' => 99997,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'attacker_id',
                'defender_id',
                'move_id',
            ]);
    }
}
