<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreUserPokemonRequest;
use App\Models\Pokemon;
use App\Models\UserPokemon;
use Illuminate\Http\JsonResponse;

class UserPokemonController extends Controller
{
    public function index(): JsonResponse
    {
        $userPokemons = UserPokemon::with(['pokemon', 'moves'])->get();

        return response()->json([
            'success' => true,
            'data' => $userPokemons,
        ], 200);
    }

    public function store(StoreUserPokemonRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $pokemon = Pokemon::findOrFail($validated['pokemon_id']);

        $userPokemon = UserPokemon::create([
            'pokemon_id' => $pokemon->id,
            'current_hp' => $pokemon->hp,
        ]);

        $userPokemon->moves()->sync($validated['move_ids']);

        $userPokemon->load(['pokemon', 'moves']);

        return response()->json([
            'success' => true,
            'data' => $userPokemon,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $userPokemon = UserPokemon::with(['pokemon', 'moves'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $userPokemon,
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $userPokemon = UserPokemon::findOrFail($id);
        $userPokemon->delete();

        return response()->json([
            'success' => true,
            'data' => null,
        ], 200);
    }
}
