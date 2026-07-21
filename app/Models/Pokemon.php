<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Pokemon extends Model
{
    /** @use HasFactory<\Database\Factories\PokemonFactory> */
    use HasFactory;

    protected $table = 'pokemons';

    protected $fillable = [
        'name',
        'type',
        'level',
        'hp',
        'attack',
        'defense',
        'sp_attack',
        'sp_defense',
        'speed',
    ];

    protected function casts(): array
    {
        return [
            'level'      => 'integer',
            'hp'         => 'integer',
            'attack'     => 'integer',
            'defense'    => 'integer',
            'sp_attack'  => 'integer',
            'sp_defense' => 'integer',
            'speed'      => 'integer',
        ];
    }

    public function userPokemons(): HasMany
    {
        return $this->hasMany(UserPokemon::class);
    }

    public function moves(): HasManyThrough
    {
        return $this->hasManyThrough(
            Move::class,
            UserPokemon::class,
            'pokemon_id',      // Clave foránea en user_pokemons
            'id',              // Clave foránea en moves (o pivote)
            'id',              // Clave local en pokemons
            'id'               // Clave local en user_pokemons
        );
    }
}