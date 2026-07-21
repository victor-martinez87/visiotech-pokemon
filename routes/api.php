<?php

declare(strict_types=1);

use App\Http\Controllers\Api\BattleController;
use Illuminate\Support\Facades\Route;

Route::post('/battle/calculate', [BattleController::class, 'calculate']);
