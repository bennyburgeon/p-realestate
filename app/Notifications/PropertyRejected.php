<?php

namespace App\Notifications;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PropertyRejected extends Notification
{
    use Queueable;

    public function __construct(public readonly Property $property, public readonly ?string $reason = null) {}

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
            'message' => "Your property \"{$this->property->title}\" was rejected.".($this->reason ? " Reason: {$this->reason}" : ''),
            'url' => route('properties.mine'),
        ];
    }
}
