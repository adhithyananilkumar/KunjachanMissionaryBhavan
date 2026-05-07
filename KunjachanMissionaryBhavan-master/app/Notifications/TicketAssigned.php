<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\SupportTicket;

class TicketAssigned extends Notification
{
    use Queueable;

    public function __construct(
        public SupportTicket $ticket,
        public ?string $byName = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'type' => 'ticket_assigned',
            'ticket_public_id' => $this->ticket->public_id,
            'ticket_title' => $this->ticket->title,
            'message' => 'Ticket assigned'.($this->byName ? " by {$this->byName}" : ''),
            'link' => route('developer.tickets.show', $this->ticket),
        ]);
    }
}
