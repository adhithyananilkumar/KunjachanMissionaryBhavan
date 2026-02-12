<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

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
                    if(!isset($n->data['type']) && isset($n->data['ticket_id'])){
                        $data = $n->data;
                        $data['type'] = 'ticket_reply';
                        $n->data = $data;
                    }
                }
            $count = $items->count();
        }
        $view->with([
            'unreadNotificationsCount' => $count,
            'unreadNotifications' => $items,
        ]);
    }
}
