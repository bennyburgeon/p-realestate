<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertySearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'intent' => ['nullable', 'in:buy,rent,lease'],
            'nature' => ['nullable', 'in:residential,commercial'],
            'category' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'locality' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string', 'max:100'],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'min:0'],
            'area_min' => ['nullable', 'numeric', 'min:0'],
            'area_max' => ['nullable', 'numeric', 'min:0'],
            'bedrooms' => ['nullable', 'integer', 'min:1', 'max:10'],
            'bathrooms' => ['nullable', 'integer', 'min:1', 'max:10'],
            'furnishing' => ['nullable', 'in:unfurnished,semi_furnished,furnished'],
            'verified_only' => ['nullable', 'boolean'],
            'featured_only' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'in:newest,price_low,price_high'],
            'view' => ['nullable', 'in:grid,list'],
        ];
    }
}
