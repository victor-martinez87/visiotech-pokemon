<?php

declare(strict_types=1);

use App\Http\Controllers\Api\BattleController;
use App\Http\Controllers\Api\UserPokemonController;
use Illuminate\Support\Facades\Route;

Route::post('/battle/calculate', [BattleController::class, 'calculate']);

Route::apiResource('user-pokemons', UserPokemonController::class);
