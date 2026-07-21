<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Move;

class MoveSeeder extends Seeder
{
    public function run(): void
    {
        $moves = [
            ['name' => 'Thunderbolt', 'type' => 'electric', 'power' => 90],
            ['name' => 'Flamethrower', 'type' => 'fire', 'power' => 90],
            ['name' => 'Water Gun', 'type' => 'water', 'power' => 40],
            ['name' => 'Vine Whip', 'type' => 'grass', 'power' => 45],
            ['name' => 'Quick Attack', 'type' => 'normal', 'power' => 40],
            ['name' => 'Tackle', 'type' => 'normal', 'power' => 40],
        ];

        foreach ($moves as $move) {
            Move::updateOrCreate(['name' => $move['name']], $move);
        }
    }
}