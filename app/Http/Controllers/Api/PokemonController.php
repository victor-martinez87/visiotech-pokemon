<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Move;
use App\Models\Pokemon;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class PokemonController extends Controller
{
    /**
     * @OA\Get(
     *     path="/pokemons/{id}/moves",
     *     summary="Obtener los movimientos asignados a un Pokémon específico",
     *     description="Devuelve el Pokémon junto con la lista de movimientos que tiene actualmente equipados.",
     *     tags={"Pokemons"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del Pokémon",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="pokemon", type="string", example="Pikachu"),
     *                 @OA\Property(
     *                     property="moves",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Thunderbolt"),
     *                         @OA\Property(property="type", type="string", example="Eléctrico"),
     *                         @OA\Property(property="power", type="integer", example=90)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pokémon no encontrado"
     *     )
     * )
     */
    public function moves(int $id): JsonResponse
    {
        $pokemon = Pokemon::with('moves')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'pokemon' => $pokemon->name,
                'moves'   => $pokemon->moves,
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/pokemons/{id}/possible-moves",
     *     summary="Obtener los movimientos posibles de un Pokémon",
     *     description="Devuelve todos los movimientos que el Pokémon puede aprender basándose en su tipo (coincidencia de tipo o tipo 'normal').",
     *     tags={"Pokemons"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del Pokémon",
     *         @OA\Schema(type="integer", example=4)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="pokemon", type="string", example="Charmander"),
     *                 @OA\Property(property="type", type="string", example="Fuego"),
     *                 @OA\Property(
     *                     property="possible_moves",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=2),
     *                         @OA\Property(property="name", type="string", example="Flamethrower"),
     *                         @OA\Property(property="type", type="string", example="Fuego")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pokémon no encontrado"
     *     )
     * )
     */
    public function possibleMoves(int $id): JsonResponse
    {
        $pokemon = Pokemon::findOrFail($id);

        // Busca todos los movimientos que coinciden con el tipo del Pokémon o son de tipo 'normal'
        $possibleMoves = Move::where('type', $pokemon->type)
            ->orWhere('type', 'normal')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'pokemon'        => $pokemon->name,
                'type'           => $pokemon->type,
                'possible_moves' => $possibleMoves,
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/moves/{id}/pokemons",
     *     summary="Obtener una lista con los Pokémons que comparten un mismo movimiento",
     *     description="Devuelve todos los Pokémons que pueden aprender este movimiento basándose en compatibilidad de tipos.",
     *     tags={"Moves"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del movimiento",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="move", type="string", example="Flamethrower"),
     *                 @OA\Property(property="type", type="string", example="Fuego"),
     *                 @OA\Property(
     *                     property="pokemons",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=4),
     *                         @OA\Property(property="name", type="string", example="Charmander"),
     *                         @OA\Property(property="type", type="string", example="Fuego")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Movimiento no encontrado"
     *     )
     * )
     */
    public function pokemonsByMove(int $moveId): JsonResponse
    {
        $move = Move::findOrFail($moveId);

        // Pokémons que pueden aprender/usar este movimiento por coincidencia de tipo o tipo normal
        $pokemons = Pokemon::where('type', $move->type)
            ->orWhereRaw('? = "normal"', [$move->type])
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'move'     => $move->name,
                'type'     => $move->type,
                'pokemons' => $pokemons,
            ],
        ]);
    }
}