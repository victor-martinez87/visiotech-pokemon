<?php

namespace App\Http\Controllers;

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
abstract class Controller
{
    //
}