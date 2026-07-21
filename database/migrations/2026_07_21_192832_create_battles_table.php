<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('battles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pokemon_1_id')->constrained('pokemons')->cascadeOnDelete();
            $table->foreignId('pokemon_2_id')->constrained('pokemons')->cascadeOnDelete();
            $table->integer('pokemon_1_hp');
            $table->integer('pokemon_2_hp');
            $table->foreignId('current_turn_pokemon_id')->constrained('pokemons');
            $table->enum('status', ['in_progress', 'finished'])->default('in_progress');
            $table->foreignId('winner_id')->nullable()->constrained('pokemons');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('battles');
    }
};