<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\LabTest;

class LabResultUploaded extends Notification
{
    use Queueable;

    public function __construct(public LabTest $labTest){}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        $labTest = $this->labTest->fresh(['inmate','updatedBy']);
        return new DatabaseMessage([
            'type' => 'lab_result_uploaded',
            'lab_test_id' => $labTest->id,
            'test_name' => $labTest->test_name,
            'inmate_name' => $labTest->inmate?->full_name,
            'updated_by' => $labTest->updatedBy?->name,
            'completed_date' => $labTest->completed_date,
            'link' => route('doctor.lab-tests.show', $labTest),
        ]);
    }

    public function toArray($notifiable): array
    {
        $labTest = $this->labTest->fresh(['inmate','updatedBy']);
        return [
            'type' => 'lab_result_uploaded',
            'lab_test_id' => $labTest->id,
            'test_name' => $labTest->test_name,
            'inmate_name' => $labTest->inmate?->full_name,
            'updated_by' => $labTest->updatedBy?->name,
            'completed_date' => $labTest->completed_date,
            'link' => route('doctor.lab-tests.show', $labTest),
        ];
    }
}
