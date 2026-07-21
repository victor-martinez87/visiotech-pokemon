<?php

declare(strict_types=1);

use App\Http\Controllers\Api\BattleController;
use App\Http\Controllers\Api\UserPokemonController;
use App\Http\Controllers\Api\PokemonController;
use Illuminate\Support\Facades\Route;

Route::post('/battle/calculate', [BattleController::class, 'calculate']);
Route::post('/battles/start', [BattleController::class, 'start']);
Route::post('/battles/{id}/turn', [BattleController::class, 'turn']);

Route::apiResource('user-pokemons', UserPokemonController::class);
Route::get('/pokemons/{id}/moves', [PokemonController::class, 'moves']);
Route::get('/pokemons/{id}/possible-moves', [PokemonController::class, 'possibleMoves']);
Route::get('/moves/{id}/pokemons', [PokemonController::class, 'pokemonsByMove']);
