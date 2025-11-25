<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmergencyAppointmentScheduled extends Notification
{
    use Queueable;

    public function __construct(public Appointment $appointment) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Emergency Appointment Scheduled')
            ->line('An emergency appointment has been scheduled for '.$this->appointment->inmate->full_name.' on '.$this->appointment->scheduled_for->format('Y-m-d').'.')
            ->action('View Calendar', route('doctor.appointments.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'emergency_appointment',
            'inmate_name' => $this->appointment->inmate->full_name,
            'scheduled_for' => $this->appointment->scheduled_for->toDateString(),
            'link' => route('doctor.appointments.index'),
        ];
    }
}
