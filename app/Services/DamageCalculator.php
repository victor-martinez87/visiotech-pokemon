<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Move;
use App\Models\Pokemon;

class DamageCalculator
{
    /**
     * Tabla de efectividad entre tipos.
     * Retorna el multiplicador (2.0 = Súper efectivo, 0.5 = Poco efectivo, 1.0 = Neutro)
     */
    private const TYPE_CHART = [
        'fire' => [
            'grass' => 2.0,
            'water' => 0.5,
            'fire'  => 0.5,
        ],
        'water' => [
            'fire'  => 2.0,
            'grass' => 0.5,
            'water' => 0.5,
        ],
        'grass' => [
            'water' => 2.0,
            'fire'  => 0.5,
            'grass' => 0.5,
        ],
        'electric' => [
            'water'    => 2.0,
            'electric' => 0.5,
            'grass'    => 0.5,
        ],
        'normal' => [],
    ];

    /**
     * Tipos considerados especiales (usan sp_attack / sp_defense)
     */
    private const SPECIAL_TYPES = ['fire', 'water', 'grass', 'electric'];

    /**
     * Calcula el daño exacto recibido.
     * 
     * @return array{
     *     damage: int,
     *     effectiveness: float,
     *     is_special: bool,
     *     message: string
     * }
     */
    public function calculate(Pokemon $attacker, Pokemon $defender, Move $move): array
    {
        $moveType = strtolower($move->type);
        $defenderType = strtolower($defender->type);

        // 1. Obtener multiplicador de efectividad (por defecto 1.0 = neutro)
        $effectiveness = self::TYPE_CHART[$moveType][$defenderType] ?? 1.0;

        // 2. Determinar si es ataque Especial o Físico
        $isSpecial = in_array($moveType, self::SPECIAL_TYPES, true);

        $attackStat = $isSpecial ? $attacker->sp_attack : $attacker->attack;
        $defenseStat = $isSpecial ? $defender->sp_defense : $defender->defense;

        // Evitar división por cero
        $defenseStat = max($defenseStat, 1);

        // 3. Fórmula base de daño
        $levelFactor = ($attacker->level * 2 / 5) + 2;
        $statRatio = $attackStat / $defenseStat;
        $baseDamage = (($levelFactor * $move->power * $statRatio) / 50) + 2;

        // 4. Aplicar efectividad y redondear
        $finalDamage = (int) max(1, round($baseDamage * $effectiveness));

        return [
            'damage'        => $finalDamage,
            'effectiveness' => $effectiveness,
            'is_special'    => $isSpecial,
            'message'       => $this->getEffectivenessMessage($effectiveness),
        ];
    }

    private function getEffectivenessMessage(float $effectiveness): string
    {
        return match (true) {
            $effectiveness >= 2.0 => '¡Es muy eficaz!',
            $effectiveness <= 0.5 && $effectiveness > 0.0 => 'No es muy eficaz...',
            $effectiveness === 0.0 => 'No afecta al Pokémon rival.',
            default => 'Ataque neutro.',
        };
    }
}