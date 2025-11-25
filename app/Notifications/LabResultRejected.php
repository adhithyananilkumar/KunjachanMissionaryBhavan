<?php
namespace App\Notifications;

use App\Models\LabTest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LabResultRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public LabTest $labTest, public string $reason = '') {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'lab_result_rejected',
            'lab_test_id' => $this->labTest->id,
            'inmate_id' => $this->labTest->inmate_id,
            'test_name' => $this->labTest->test_name,
            'patient_name' => $this->labTest->inmate?->full_name,
            'doctor' => auth()->user()?->name,
            'reason' => $this->reason,
            'message' => 'Lab report rejected by doctor'.($this->reason ? ': '.$this->reason : '.'),
            'url' => route($notifiable->role.'.lab-tests.show', $this->labTest),
        ];
    }
}
