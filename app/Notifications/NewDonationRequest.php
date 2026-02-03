<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDonationRequest extends Notification
{
    use Queueable;

    public function __construct(public $request)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'donation_request',
            'amount' => $this->request->amount,
            'donor_name' => $this->request->donor_name,
            'request_id' => $this->request->id,
            'title' => 'New Donation Request',
            'message' => '₹' . number_format($this->request->amount) . ' from ' . $this->request->donor_name,
            'link' => route('system_admin.institutions.show', $this->request->institution_id) . '#donations',
        ];
    }
}
