<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Move;
use App\Models\Pokemon;
use App\Services\DamageCalculator;
use PHPUnit\Framework\TestCase;

class DamageCalculatorTest extends TestCase
{
    private DamageCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new DamageCalculator();
    }

    public function test_super_effective_damage_multiplier(): void
    {
        // Charmander (Fuego) atacando a Bulbasaur (Planta) con Flamethrower
        $attacker = new Pokemon(["type" => "fire", "sp_attack" => 60, "level" => 50]);
        $defender = new Pokemon(["type" => "grass", "sp_defense" => 65, "level" => 50]);
        $move     = new Move(["type" => "fire", "power" => 90]);

        $result = $this->calculator->calculate($attacker, $defender, $move);

        $this->assertEquals(2.0, $result["effectiveness"]);
        $this->assertGreaterThan(0, $result["damage"]);
        $this->assertEquals("¡Es muy eficaz!", $result["message"]);
    }

    public function test_not_very_effective_damage_multiplier(): void
    {
        // Charmander (Fuego) atacando a Squirtle (Agua) con Flamethrower
        $attacker = new Pokemon(["type" => "fire", "sp_attack" => 60, "level" => 50]);
        $defender = new Pokemon(["type" => "water", "sp_defense" => 65, "level" => 50]);
        $move     = new Move(["type" => "fire", "power" => 90]);

        $result = $this->calculator->calculate($attacker, $defender, $move);

        $this->assertEquals(0.5, $result["effectiveness"]);
        $this->assertEquals("No es muy eficaz...", $result["message"]);
    }
}

