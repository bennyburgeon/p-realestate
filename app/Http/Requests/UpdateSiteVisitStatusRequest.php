<?php

namespace App\Http\Requests;

use App\Models\SiteVisit;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteVisitStatusRequest extends FormRequest
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
            'status' => ['required', 'in:'.implode(',', [
                SiteVisit::STATUS_SCHEDULED,
                SiteVisit::STATUS_COMPLETED,
                SiteVisit::STATUS_CANCELLED,
                SiteVisit::STATUS_NO_SHOW,
            ])],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
