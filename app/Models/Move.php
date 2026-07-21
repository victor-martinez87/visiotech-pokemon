<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Move extends Model
{
    /** @use HasFactory<\Database\Factories\MoveFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'type',
        'power',
    ];

    protected function casts(): array
    {
        return [
            'power' => 'integer',
        ];
    }

    public function userPokemons(): BelongsToMany
    {
        return $this->belongsToMany(UserPokemon::class, 'user_pokemon_moves');
    }
}
