<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarkPropertySoldRequest extends FormRequest
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
            'sold_at' => ['nullable', 'date'],
            'sold_price' => ['nullable', 'numeric', 'min:0'],
            'buyer_source' => ['nullable', 'in:platform_enquiry,external,other'],
            'sold_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
