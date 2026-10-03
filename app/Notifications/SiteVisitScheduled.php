<?php

namespace App\Notifications;

use App\Models\SiteVisit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SiteVisitScheduled extends Notification
{
    use Queueable;

    public function __construct(public readonly SiteVisit $visit) {}

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
            'property_id' => $this->visit->property_id,
            'title' => $this->visit->property->title,
            'message' => "A site visit for \"{$this->visit->property->title}\" has been scheduled for {$this->visit->scheduled_at->format('d M Y, g:ia')}.",
            'url' => route('properties.show', $this->visit->property),
        ];
    }
}
