<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_id' => ['required', 'uuid', 'exists:stores,id'],
            'external_id' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'url' => ['required', 'url'],
            'currency' => ['required', 'string', 'size:3'], // Required for sync
            'brand_id' => ['nullable', 'uuid', 'exists:brands,id'],
            'category_id' => ['nullable', 'uuid', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'additional_data' => ['nullable', 'array'],
            'images' => ['nullable', 'array'],
            'images.*.image_url' => ['required', 'url'],
            'images.*.main' => ['boolean'],
        ];
    }
}
