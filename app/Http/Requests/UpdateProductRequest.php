<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_id' => ['sometimes', 'uuid', 'exists:stores,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'url' => ['sometimes', 'url', 'unique:products,url,' . $this->route('id')],
            'external_id' => ['sometimes', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'size:3'],
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
