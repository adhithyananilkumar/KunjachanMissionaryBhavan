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
        return new DatabaseMessage([
            'ticket_id' => $this->ticket->id,
            'ticket_title' => $this->ticket->title,
            'reply_excerpt' => str(\Illuminate\Support\Str::limit($this->reply->message,120)),
            'replied_by' => $this->reply->user->name,
        ]);
    }
}
