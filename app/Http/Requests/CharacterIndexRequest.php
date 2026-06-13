<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CharacterIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'name' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:alive,dead,unknown'],
            'species' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'in:male,female,genderless,unknown'],
            'type' => ['nullable', 'string', 'max:100'],
        ];
    }
}