<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\SupportTicket;
use App\Models\TicketReply;

class NewTicketReply extends Notification
{
    use Queueable;

    public function __construct(public SupportTicket $ticket, public TicketReply $reply){}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        $isDeveloper = ($notifiable->role ?? null) === 'developer';

        return new DatabaseMessage([
            'type' => 'ticket_reply',
            'ticket_public_id' => $this->ticket->public_id,
            'ticket_title' => $this->ticket->title,
            'reply_excerpt' => \Illuminate\Support\Str::limit((string) $this->reply->message, 120),
            'replied_by' => $this->reply->user->name,
            'link' => $isDeveloper
                ? route('developer.tickets.show', $this->ticket)
                : route('tickets.show', $this->ticket),
        ]);
    }
}
