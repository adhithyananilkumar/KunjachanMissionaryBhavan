<?php

namespace App\Notifications;

use App\Models\Inmate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InmateBirthday extends Notification
{
    use Queueable;

    public function __construct(public Inmate $inmate) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $name = $this->inmate->full_name;
        $age = $this->inmate->date_of_birth ? $this->inmate->date_of_birth->age : null;
        // Role-aware link to inmate profile
        $role = method_exists($notifiable, 'getAttribute') ? ($notifiable->role ?? null) : null;
        $link = null;
        try {
            if ($role === 'system_admin') {
                $link = route('system_admin.inmates.show', $this->inmate);
            } elseif ($role === 'admin') {
                $link = route('admin.inmates.show', $this->inmate);
            } elseif ($role === 'staff') {
                $link = route('staff.inmates.show', $this->inmate);
            }
        } catch (\Throwable $e) { /* ignore bad route contexts */ }

        return [
            'type' => 'inmate_birthday',
            'inmate_id' => $this->inmate->id,
            'inmate_name' => $name,
            'age' => $age,
            'title' => 'Inmate Birthday',
            'message' => $age ? "$name turns $age today." : "$name has a birthday today.",
            'link' => $link,
        ];
    }
}
