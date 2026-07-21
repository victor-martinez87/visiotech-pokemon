<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
            'level' => 'integer',
            'hp' => 'integer',
            'attack' => 'integer',
            'defense' => 'integer',
            'sp_attack' => 'integer',
            'sp_defense' => 'integer',
            'speed' => 'integer',
        ];
    }

    public function userPokemons(): HasMany
    {
        return $this->hasMany(UserPokemon::class);
    }
}
