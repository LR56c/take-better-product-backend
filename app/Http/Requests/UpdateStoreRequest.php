<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country_id' => ['sometimes', 'uuid', 'exists:countries,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'url' => ['nullable', 'url'],
            'thumbnail' => ['nullable', 'string'],
            'type' => ['sometimes', Rule::in(['supermarket', 'pharmacy', 'technology', 'clothes', 'pets', 'library'])],
        ];
    }
}
