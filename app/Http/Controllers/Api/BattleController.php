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
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Pokémon Battle API",
 *      description="API para la gestión de Pokémons, Movimientos y simulación de Combates"
 * )
 *
 * @OA\Server(
 *      url="/api",
 *      description="Servidor API Local"
 * )
 */

class BattleController extends Controller
{
    public function __construct(
        private readonly DamageCalculator $damageCalculator,
    ) {}
/**
     * @OA\Post(
     *     path="/battle/calculate",
     *     summary="Calcula el daño de un ataque (Sin guardar estado)",
     *     description="Se le pasa un Pokémon, el movimiento y su rival, y devuelve el cálculo del daño sin iniciar un combate real.",
     *     tags={"Battles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"attacker_id", "defender_id", "move_id"},
     *             @OA\Property(property="attacker_id", type="integer", example=1, description="ID del Pokémon atacante"),
     *             @OA\Property(property="defender_id", type="integer", example=4, description="ID del Pokémon defensor"),
     *             @OA\Property(property="move_id", type="integer", example=2, description="ID del movimiento usado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cálculo de daño exitoso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="attacker", type="object", @OA\Property(property="id", type="integer"), @OA\Property(property="name", type="string"), @OA\Property(property="type", type="string"), @OA\Property(property="level", type="integer")),
     *                 @OA\Property(property="defender", type="object", @OA\Property(property="id", type="integer"), @OA\Property(property="name", type="string"), @OA\Property(property="type", type="string"), @OA\Property(property="level", type="integer")),
     *                 @OA\Property(property="move", type="object", @OA\Property(property="id", type="integer"), @OA\Property(property="name", type="string"), @OA\Property(property="type", type="string"), @OA\Property(property="power", type="integer")),
     *                 @OA\Property(property="calculation", type="object", @OA\Property(property="damage", type="integer", example=42), @OA\Property(property="effectiveness", type="number", example=2.0))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Errores de validación en los parámetros enviados"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pokémon o Movimiento no encontrado"
     *     )
     * )
     */
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
     * @OA\Post(
     *     path="/battles",
     *     summary="Inicia un nuevo combate entre dos Pokémons",
     *     description="Crea una instancia de combate y determina quién ataca primero según la estadística de Velocidad.",
     *     tags={"Battles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"pokemon_1_id", "pokemon_2_id"},
     *             @OA\Property(property="pokemon_1_id", type="integer", example=1, description="ID del primer Pokémon (Ej: Pikachu)"),
     *             @OA\Property(property="pokemon_2_id", type="integer", example=4, description="ID del segundo Pokémon (Ej: Charmander)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Combate iniciado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Combate iniciado."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="pokemon_1_hp", type="integer", example=35),
     *                 @OA\Property(property="pokemon_2_hp", type="integer", example=39),
     *                 @OA\Property(property="current_turn_pokemon_id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="in_progress")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Errores de validación (ej. el mismo Pokémon elegido dos veces)"
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/battles/{id}/turn",
     *     summary="Ejecuta un turno de combate",
     *     description="Resta PS al defensor aplicando la fórmula de daño. Cambia el turno o finaliza el combate si los PS llegan a 0.",
     *     tags={"Battles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del combate en curso",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"move_id"},
     *             @OA\Property(property="move_id", type="integer", example=3, description="ID del movimiento a utilizar por el Pokémon que tiene el turno")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Turno ejecutado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="battle", type="object", description="Estado actualizado del combate"),
     *                 @OA\Property(property="calculation", type="object", description="Desglose del daño aplicado")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="El combate ya ha finalizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="El combate ya ha finalizado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Combate o movimiento no encontrado"
     *     )
     * )
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