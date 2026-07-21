<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Move;
use App\Models\Pokemon;
use Illuminate\Http\JsonResponse;

class PokemonController extends Controller
{
    /**
     * 1. Consulta para obtener los movimientos asignados a un Pokémon específico.
     * GET /api/pokemons/{id}/moves
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
     * 2. Consulta para obtener los movimientos posibles de un Pokémon (según su tipo).
     * GET /api/pokemons/{id}/possible-moves
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
     * 3. Consulta para obtener una lista con los Pokémons que comparten un mismo movimiento.
     * GET /api/moves/{id}/pokemons
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