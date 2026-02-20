<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\SupportTicket;

class NotificationComposer
{
    public function compose(View $view): void
    {
        $count = 0; $items = collect();
        if (Auth::check() && Schema::hasTable('notifications')) {
            $user = Auth::user();
                $items = $user->unreadNotifications()->latest()->take(10)->get();
                // Backfill type for legacy ticket reply notifications if missing
                foreach($items as $n){
                    $data = $n->data ?? [];
                    if (!isset($data['type']) && (isset($data['ticket_id']) || isset($data['ticket_public_id']))) {
                        $data = $n->data;
                        $data['type'] = 'ticket_reply';
                    }

                    if (!isset($data['link'])) {
                        $publicId = $data['ticket_public_id'] ?? null;
                        if (!$publicId && isset($data['ticket_id'])) {
                            $publicId = SupportTicket::where('id', (int) $data['ticket_id'])->value('public_id');
                        }
                        if ($publicId) {
                            $data['ticket_public_id'] = $publicId;
                            $data['link'] = route(($user->role ?? null) === 'developer' ? 'developer.tickets.show' : 'tickets.show', $publicId);
                        }
                    }

                    $n->data = $data;
                }
            $count = $items->count();
        }
        $view->with([
            'unreadNotificationsCount' => $count,
            'unreadNotifications' => $items,
        ]);
    }
}
