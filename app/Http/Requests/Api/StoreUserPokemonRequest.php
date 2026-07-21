<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserPokemonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'pokemon_id' => ['required', 'integer', 'exists:pokemons,id'],
            'move_ids' => ['required', 'array', 'max:4'],
            'move_ids.*' => ['integer', 'exists:moves,id'],
        ];
    }
}
