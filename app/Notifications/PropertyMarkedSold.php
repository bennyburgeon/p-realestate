<?php

namespace App\Notifications;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PropertyMarkedSold extends Notification
{
    use Queueable;

    public function __construct(public readonly Property $property) {}

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
            'message' => "Congratulations! \"{$this->property->title}\" has been marked as sold.",
            'url' => route('properties.show', $this->property),
        ];
    }
}
