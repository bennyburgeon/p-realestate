<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequirementResponseStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(['owner', 'agent', 'developer', 'admin', 'super_admin']) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'property_id' => ['nullable', 'exists:properties,id'],
            'message' => ['required', 'string', 'max:1000'],
        ];
    }
}
