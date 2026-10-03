<?php

namespace App\Notifications;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PropertyChangesRequested extends Notification
{
    use Queueable;

    public function __construct(public readonly Property $property, public readonly string $reason) {}

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
            'property_id' => $this->property->id,
            'title' => $this->property->title,
            'message' => "Changes are needed on \"{$this->property->title}\" before it can be published: {$this->reason}",
            'url' => route('properties.sell.edit', $this->property),
        ];
    }
}
