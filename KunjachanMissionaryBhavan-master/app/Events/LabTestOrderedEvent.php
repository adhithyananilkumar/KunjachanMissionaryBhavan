<?php
namespace App\Events;
use App\Models\LabTest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LabTestOrderedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public LabTest $labTest) {}

    public function broadcastOn(): array
    {
        return [ new PrivateChannel('institution.'.$this->labTest->inmate->institution_id) ];
    }

    public function broadcastWith(): array
    {
        return [
            'lab_test_id' => $this->labTest->id,
            'inmate_id' => $this->labTest->inmate_id,
            'test_name' => $this->labTest->test_name,
            'ordered_at' => optional($this->labTest->ordered_date)->toIso8601String(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'lab-test.ordered';
    }
}
