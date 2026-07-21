<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Battle extends Model
{
    use HasFactory;

    protected $fillable = [
        'pokemon_1_id',
        'pokemon_2_id',
        'pokemon_1_hp',
        'pokemon_2_hp',
        'current_turn_pokemon_id',
        'status',
        'winner_id',
    ];

    protected function casts(): array
    {
        return [
            'pokemon_1_hp' => 'integer',
            'pokemon_2_hp' => 'integer',
        ];
    }

    public function pokemon1(): BelongsTo
    {
        return $this->belongsTo(Pokemon::class, 'pokemon_1_id');
    }

    public function pokemon2(): BelongsTo
    {
        return $this->belongsTo(Pokemon::class, 'pokemon_2_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Pokemon::class, 'winner_id');
    }
}