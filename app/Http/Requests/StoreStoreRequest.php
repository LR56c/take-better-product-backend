<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country_id' => ['required', 'uuid', 'exists:countries,id'],
            'name' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'url'],
            'thumbnail' => ['nullable', 'string'],
            'type' => ['required', Rule::in(['supermarket', 'pharmacy', 'technology', 'clothes', 'pets', 'library'])],
        ];
    }
}
