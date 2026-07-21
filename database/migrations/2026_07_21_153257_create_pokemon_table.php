<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pokemons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->unsignedInteger('level');
            $table->unsignedInteger('hp');
            $table->unsignedInteger('attack');
            $table->unsignedInteger('defense');
            $table->unsignedInteger('sp_attack');
            $table->unsignedInteger('sp_defense');
            $table->unsignedInteger('speed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pokemons');
    }
};
