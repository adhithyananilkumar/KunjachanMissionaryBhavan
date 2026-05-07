<?php

namespace App\Notifications;

use App\Models\Inmate;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TransferOfCareNotification extends Notification
{
    use Queueable;

    public function __construct(public Inmate $inmate, public string $direction = 'incoming', public ?User $otherDoctor = null) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $isIncoming = $this->direction === 'incoming';
        return [
            'type' => 'transfer_of_care',
            'direction' => $this->direction,
            'inmate_id' => $this->inmate->id,
            'inmate_name' => $this->inmate->full_name,
            'other_doctor' => $this->otherDoctor?->name,
            'message' => $isIncoming
                ? ('A new patient has been assigned to you: '.$this->inmate->full_name)
                : ('Patient transferred away: '.$this->inmate->full_name.' to '.$this->otherDoctor?->name),
            'link' => route('doctor.inmates.show', $this->inmate),
        ];
    }
}
