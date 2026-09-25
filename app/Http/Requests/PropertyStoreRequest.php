<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\HasPropertyRules;
use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;

class PropertyStoreRequest extends FormRequest
{
    use HasPropertyRules;

    public function authorize(): bool
    {
        return $this->user()->can('create', Property::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->propertyRules(required: true);
    }
}
