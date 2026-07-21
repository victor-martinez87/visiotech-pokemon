<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_pokemons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pokemon_id')->constrained('pokemons')->cascadeOnDelete();
            $table->unsignedInteger('current_hp')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_pokemons');
    }
};
