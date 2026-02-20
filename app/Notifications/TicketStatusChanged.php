<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\SupportTicket;

class TicketStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public SupportTicket $ticket,
        public string $oldStatus,
        public string $newStatus,
        public ?string $byName = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'type' => 'ticket_status_changed',
            'ticket_public_id' => $this->ticket->public_id,
            'ticket_title' => $this->ticket->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => "Status changed to {$this->newStatus}".($this->byName ? " by {$this->byName}" : ''),
            'link' => route($notifiable->role === 'developer' ? 'developer.tickets.show' : 'tickets.show', $this->ticket),
        ]);
    }
}
