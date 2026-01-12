<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\LabTest;

class LabTestOrdered extends Notification
{
    use Queueable;

    public function __construct(public LabTest $labTest){}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        $labTest = $this->labTest->fresh(['inmate','orderedBy']);
        return new DatabaseMessage([
            'type' => 'lab_test_ordered',
            'lab_test_id' => $labTest->id,
            'test_name' => $labTest->test_name,
            'inmate_name' => $labTest->inmate?->full_name,
            'ordered_by' => $labTest->orderedBy?->name,
            'ordered_at' => $labTest->ordered_date,
            'link' => route('nurse.lab-tests.show', $labTest),
        ]);
    }

    public function toArray($notifiable): array
    {
        $labTest = $this->labTest->fresh(['inmate','orderedBy']);
        return [
            'type' => 'lab_test_ordered',
            'lab_test_id' => $labTest->id,
            'test_name' => $labTest->test_name,
            'inmate_name' => $labTest->inmate?->full_name,
            'ordered_by' => $labTest->orderedBy?->name,
            'ordered_at' => $labTest->ordered_date,
            'link' => route('nurse.lab-tests.show', $labTest),
        ];
    }
}
