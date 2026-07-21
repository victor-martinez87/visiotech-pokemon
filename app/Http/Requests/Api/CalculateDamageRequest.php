<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CalculateDamageRequest extends FormRequest
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
            'attacker_id' => ['required', 'integer', 'exists:pokemons,id'],
            'defender_id' => ['required', 'integer', 'exists:pokemons,id'],
            'move_id' => ['required', 'integer', 'exists:moves,id'],
        ];
    }
}
