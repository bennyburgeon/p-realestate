<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            // property
            [Status::PROPERTY_DRAFT, 'property', 'draft', 'Draft', 'gray'],
            [Status::PROPERTY_PENDING_REVIEW, 'property', 'pending_review', 'Pending Review', 'amber'],
            [Status::PROPERTY_PUBLISHED, 'property', 'published', 'Published', 'green'],
            [Status::PROPERTY_AVAILABLE, 'property', 'available', 'Available', 'green'],
            [Status::PROPERTY_RESERVED, 'property', 'reserved', 'Reserved', 'amber'],
            [Status::PROPERTY_UNDER_NEGOTIATION, 'property', 'under_negotiation', 'Under Negotiation', 'amber'],
            [Status::PROPERTY_SOLD, 'property', 'sold', 'Sold', 'red'],
            [Status::PROPERTY_RENTED, 'property', 'rented', 'Rented', 'red'],
            [Status::PROPERTY_LEASED, 'property', 'leased', 'Leased', 'red'],
            [Status::PROPERTY_EXPIRED, 'property', 'expired', 'Expired', 'gray'],
            [Status::PROPERTY_REJECTED, 'property', 'rejected', 'Rejected', 'red'],
            [Status::PROPERTY_SUSPENDED, 'property', 'suspended', 'Suspended', 'red'],
            [Status::PROPERTY_ARCHIVED, 'property', 'archived', 'Archived', 'gray'],

            // requirement
            [Status::REQUIREMENT_DRAFT, 'requirement', 'draft', 'Draft', 'gray'],
            [Status::REQUIREMENT_ACTIVE, 'requirement', 'active', 'Active', 'green'],
            [Status::REQUIREMENT_PAUSED, 'requirement', 'paused', 'Paused', 'amber'],
            [Status::REQUIREMENT_FULFILLED, 'requirement', 'fulfilled', 'Fulfilled', 'blue'],
            [Status::REQUIREMENT_EXPIRED, 'requirement', 'expired', 'Expired', 'gray'],
            [Status::REQUIREMENT_CLOSED, 'requirement', 'closed', 'Closed', 'gray'],

            // requirement_response
            [Status::RESPONSE_SENT, 'requirement_response', 'sent', 'Sent', 'blue'],
            [Status::RESPONSE_VIEWED, 'requirement_response', 'viewed', 'Viewed', 'amber'],
            [Status::RESPONSE_CONTACTED, 'requirement_response', 'contacted', 'Contacted', 'green'],

            // enquiry
            [Status::ENQUIRY_NEW, 'enquiry', 'new', 'New', 'blue'],
            [Status::ENQUIRY_CONTACTED, 'enquiry', 'contacted', 'Contacted', 'amber'],
            [Status::ENQUIRY_CLOSED, 'enquiry', 'closed', 'Closed', 'gray'],
        ];

        foreach ($statuses as $index => [$id, $group, $slug, $label, $color]) {
            Status::updateOrCreate(['id' => $id], [
                'group' => $group,
                'slug' => $slug,
                'label' => $label,
                'color' => $color,
                'sort_order' => $index,
            ]);
        }
    }
}
