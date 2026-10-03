<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleSiteVisitRequest extends FormRequest
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
            'scheduled_at' => ['required', 'date', 'after_or_equal:now'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
