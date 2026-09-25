<?php

namespace App\Http\Requests\Concerns;

trait HasPropertyRules
{
    /**
     * @return array<string, mixed>
     */
    protected function propertyRules(bool $required): array
    {
        $req = $required ? 'required' : 'sometimes';

        return [
            'title' => [$req, 'string', 'max:150'],
            'listing_type' => [$req, 'in:sale,rent,lease'],
            'property_category_id' => [$req, 'exists:property_categories,id'],
            'location_id' => [$req, 'exists:locations,id'],
            'description' => ['nullable', 'string', 'max:5000'],

            'price' => ['nullable', 'numeric', 'min:0', 'required_if:listing_type,sale'],
            'rent_amount' => ['nullable', 'numeric', 'min:0', 'required_if:listing_type,rent,lease'],
            'security_deposit' => ['nullable', 'numeric', 'min:0'],
            'maintenance_amount' => ['nullable', 'numeric', 'min:0'],
            'is_negotiable' => ['nullable', 'boolean'],

            'built_up_area' => ['nullable', 'numeric', 'min:0'],
            'carpet_area' => ['nullable', 'numeric', 'min:0'],
            'plot_area' => ['nullable', 'numeric', 'min:0'],
            'area_unit' => ['nullable', 'string', 'max:10'],

            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:20'],
            'bathrooms' => ['nullable', 'integer', 'min:0', 'max:20'],
            'balconies' => ['nullable', 'integer', 'min:0', 'max:20'],
            'floor_number' => ['nullable', 'integer', 'min:0'],
            'total_floors' => ['nullable', 'integer', 'min:0'],

            'furnishing_status' => ['nullable', 'in:unfurnished,semi_furnished,furnished'],
            'parking_details' => ['nullable', 'string', 'max:100'],
            'facing' => ['nullable', 'string', 'max:30'],
            'construction_year' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 5)],
            'possession_status' => ['nullable', 'in:ready_to_move,under_construction'],
            'availability_date' => ['nullable', 'date'],

            'address' => [$req, 'string', 'max:500'],
            'landmark' => ['nullable', 'string', 'max:150'],
            'pin_code' => ['nullable', 'string', 'max:10'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],

            'contact_preference' => ['nullable', 'in:phone,email,whatsapp'],
            'contact_phone' => [$req, 'string', 'max:20'],
            'contact_email' => ['nullable', 'email', 'max:150'],

            'amenity_ids' => ['nullable', 'array'],
            'amenity_ids.*' => ['exists:amenities,id'],
        ];
    }
}
