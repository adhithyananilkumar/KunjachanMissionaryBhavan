<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;


class NotificationComposer
{
    protected static $cachedNotifications = null;

    public function compose(View $view): void
    {
        if (self::$cachedNotifications !== null) {
            $view->with(self::$cachedNotifications);
            return;
        }

        $count = 0; $items = collect();

        if (Auth::check()) {
            $user = Auth::user();
            // Wrap in try-catch in case table doesn't exist yet (though it should) to avoid crashing
            try {
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
            } catch (\Exception $e) {
                // If table missing or other DB error, just show 0 notifications
            }
        }

        self::$cachedNotifications = [
            'unreadNotificationsCount' => $count,
            'unreadNotifications' => $items,
        ];

        $view->with(self::$cachedNotifications);
    }
}
