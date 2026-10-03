<?php

namespace App\Notifications;

use App\Models\Enquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewPropertyEnquiry extends Notification
{
    use Queueable;

    public function __construct(public readonly Enquiry $enquiry) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'property_id' => $this->enquiry->property_id,
            'title' => $this->enquiry->property->title,
            'message' => "New enquiry from {$this->enquiry->name} on \"{$this->enquiry->property->title}\".",
            'url' => route('properties.show', $this->enquiry->property),
        ];
    }
}
