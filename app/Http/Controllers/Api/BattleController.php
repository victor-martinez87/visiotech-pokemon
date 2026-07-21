<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CalculateDamageRequest;
use App\Models\Move;
use App\Models\Pokemon;
use App\Services\DamageCalculator;
use Illuminate\Http\JsonResponse;

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
