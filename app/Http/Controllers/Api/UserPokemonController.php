<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreUserPokemonRequest;
use App\Models\Pokemon;
use App\Models\UserPokemon;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class UserPokemonController extends Controller
{
    /**
     * @OA\Get(
     *     path="/user-pokemons",
     *     summary="Listar mis Pokémons",
     *     description="Devuelve la lista de todos los Pokémons que el usuario tiene en su equipo, incluyendo los movimientos que tienen equipados.",
     *     tags={"My Pokemons"},
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="pokemon_id", type="integer", example=25),
     *                     @OA\Property(property="current_hp", type="integer", example=35),
     *                     @OA\Property(property="pokemon", type="object", description="Datos base del Pokémon"),
     *                     @OA\Property(property="moves", type="array", @OA\Items(type="object"), description="Lista de movimientos equipados (Máximo 4)")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $userPokemons = UserPokemon::with(['pokemon', 'moves'])->get();

        return response()->json([
            'success' => true,
            'data' => $userPokemons,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/user-pokemons",
     *     summary="Añadir un Pokémon a mi equipo",
     *     description="Asigna un Pokémon base al usuario junto con un máximo de 4 movimientos. Los PS actuales se inicializan al máximo de los PS base del Pokémon.",
     *     tags={"My Pokemons"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"pokemon_id", "move_ids"},
     *             @OA\Property(property="pokemon_id", type="integer", example=1, description="ID del Pokémon base"),
     *             @OA\Property(
     *                 property="move_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 3, 5, 8},
     *                 description="Array con los IDs de los movimientos a equipar. IMPORTANTE: El límite máximo es de 4 movimientos por Pokémon según las reglas del sistema."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pokémon añadido exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object", description="El Pokémon creado con sus relaciones")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Errores de validación (ej. intentar enviar más de 4 movimientos)"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pokémon o Movimiento no encontrado"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/user-pokemons/{id}",
     *     summary="Ver detalle de uno de mis Pokémons",
     *     description="Muestra toda la información de un Pokémon específico de mi equipo.",
     *     tags={"My Pokemons"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID único del Pokémon del usuario (no el ID base)",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pokémon no encontrado"
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $userPokemon = UserPokemon::with(['pokemon', 'moves'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $userPokemon,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/user-pokemons/{id}",
     *     summary="Liberar un Pokémon de mi equipo",
     *     description="Elimina de forma permanente un Pokémon del equipo del usuario.",
     *     tags={"My Pokemons"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID único del Pokémon del usuario",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pokémon eliminado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", nullable=true, example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pokémon no encontrado"
     *     )
     * )
     */
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