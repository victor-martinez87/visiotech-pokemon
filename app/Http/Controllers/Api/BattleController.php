<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CalculateDamageRequest;
use App\Models\Battle;
use App\Models\Move;
use App\Models\Pokemon;
use App\Services\DamageCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BattleController extends Controller
{
    public function __construct(
        private readonly DamageCalculator $damageCalculator,
    ) {}

    public function calculate(CalculateDamageRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $attacker = Pokemon::findOrFail($validated['attacker_id']);
        $defender = Pokemon::findOrFail($validated['defender_id']);
        $move = Move::findOrFail($validated['move_id']);

        $calculation = $this->damageCalculator->calculate($attacker, $defender, $move);

        return response()->json([
            'success' => true,
            'data' => [
                'attacker' => $this->summarizePokemon($attacker),
                'defender' => $this->summarizePokemon($defender),
                'move' => $this->summarizeMove($move),
                'calculation' => $calculation,
            ],
        ], 200);
    }

    public function start(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pokemon_1_id' => ['required', 'exists:pokemons,id', 'different:pokemon_2_id'],
            'pokemon_2_id' => ['required', 'exists:pokemons,id'],
        ]);

        $p1 = Pokemon::findOrFail($validated['pokemon_1_id']);
        $p2 = Pokemon::findOrFail($validated['pokemon_2_id']);

        // Determinar quién empieza según Velocidad (speed)
        $firstAttackerId = ($p1->speed >= $p2->speed) ? $p1->id : $p2->id;

        $battle = Battle::create([
            'pokemon_1_id'            => $p1->id,
            'pokemon_2_id'            => $p2->id,
            'pokemon_1_hp'            => $p1->hp,
            'pokemon_2_hp'            => $p2->hp,
            'current_turn_pokemon_id' => $firstAttackerId,
            'status'                  => 'in_progress',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Combate iniciado.',
            'data'    => $battle->load(['pokemon1', 'pokemon2']),
        ], 201);
    }

    /**
     * Ejecutar un turno de combate restando PS (Parte 3 del PDF)
     * POST /api/battles/{id}/turn
     */
    public function turn(Request $request, int $id): JsonResponse
    {
        $battle = Battle::findOrFail($id);

        if ($battle->status === 'finished') {
            return response()->json([
                'success' => false,
                'message' => 'El combate ya ha finalizado.',
            ], 400);
        }

        $validated = $request->validate([
            'move_id' => ['required', 'exists:moves,id'],
        ]);

        $move = Move::findOrFail($validated['move_id']);

        // Identificar atacante y defensor según el turno
        $isP1Attacking = ($battle->current_turn_pokemon_id === $battle->pokemon_1_id);
        $attacker = $isP1Attacking ? $battle->pokemon1 : $battle->pokemon2;
        $defender = $isP1Attacking ? $battle->pokemon2 : $battle->pokemon1;

        // Calcular daño
        $calculation = $this->damageCalculator->calculate($attacker, $defender, $move);
        $damage = $calculation['damage'];

        // Aplicar daño a la vida almacenada
        if ($isP1Attacking) {
            $battle->pokemon_2_hp = max(0, $battle->pokemon_2_hp - $damage);
        } else {
            $battle->pokemon_1_hp = max(0, $battle->pokemon_1_hp - $damage);
        }

        // Comprobar si los PS del rival llegaron a 0
        if ($battle->pokemon_1_hp === 0 || $battle->pokemon_2_hp === 0) {
            $battle->status = 'finished';
            $battle->winner_id = $attacker->id;
        } else {
            // Cambiar turno
            $battle->current_turn_pokemon_id = $defender->id;
        }

        $battle->save();

        return response()->json([
            'success' => true,
            'data'    => [
                'battle'      => $battle,
                'calculation' => $calculation,
            ],
        ]);
    }

    /**
     * @return array<string, int|string>
     */
    private function summarizePokemon(Pokemon $pokemon): array
    {
        return [
            'id' => $pokemon->id,
            'name' => $pokemon->name,
            'type' => $pokemon->type,
            'level' => $pokemon->level,
        ];
    }

    /**
     * @return array<string, int|string>
     */
    private function summarizeMove(Move $move): array
    {
        return [
            'id' => $move->id,
            'name' => $move->name,
            'type' => $move->type,
            'power' => $move->power,
        ];
    }
}