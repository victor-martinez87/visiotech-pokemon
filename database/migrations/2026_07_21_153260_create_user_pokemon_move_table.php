<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_pokemon_moves', function (Blueprint $table) {
            $table->foreignId('user_pokemon_id')->constrained('user_pokemons')->cascadeOnDelete();
            $table->foreignId('move_id')->constrained('moves')->cascadeOnDelete();

            $table->primary(['user_pokemon_id', 'move_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_pokemon_moves');
    }
};
