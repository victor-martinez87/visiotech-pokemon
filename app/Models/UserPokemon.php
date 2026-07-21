<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserPokemon extends Model
{
    protected $table = 'user_pokemons';

    protected $fillable = [
        'pokemon_id',
        'current_hp',
    ];

    protected function casts(): array
    {
        return [
            'pokemon_id' => 'integer',
            'current_hp' => 'integer',
        ];
    }

    public function pokemon(): BelongsTo
    {
        return $this->belongsTo(Pokemon::class);
    }

    public function moves(): BelongsToMany
    {
        return $this->belongsToMany(Move::class, 'user_pokemon_moves');
    }
}
