<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\HasPropertyRules;
use Illuminate\Foundation\Http\FormRequest;

class PropertyUpdateRequest extends FormRequest
{
    use HasPropertyRules;

    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('property'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->propertyRules(required: false);
    }
}
