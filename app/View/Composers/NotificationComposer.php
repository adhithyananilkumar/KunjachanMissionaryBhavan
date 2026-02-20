<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\SupportTicket;

class NotificationComposer
{
    private static ?array $cached = null;

    public function compose(View $view): void
    {
        if (self::$cached !== null) {
            $view->with(self::$cached);
            return;
        }

        $count = 0; $items = collect();
        if (Auth::check() && Schema::hasTable('notifications')) {
            $user = Auth::user();
            $items = $user->unreadNotifications()->latest()->take(10)->get();
            $count = ($items->count() < 10) ? $items->count() : $user->unreadNotifications()->count();

            $missingTicketIds = [];
            foreach ($items as $n) {
                $data = $n->data ?? [];
                if (!isset($data['link']) && !isset($data['ticket_public_id']) && isset($data['ticket_id'])) {
                    $missingTicketIds[] = (int) $data['ticket_id'];
                }
            }
            $missingTicketIds = array_values(array_unique(array_filter($missingTicketIds)));

            $publicIdsByTicketId = collect();
            if (!empty($missingTicketIds)) {
                $publicIdsByTicketId = SupportTicket::whereIn('id', $missingTicketIds)
                    ->pluck('public_id', 'id');
            }

            // Backfill type/link for legacy ticket notifications if missing
            foreach ($items as $n) {
                $data = $n->data ?? [];

                if (!isset($data['type']) && (isset($data['ticket_id']) || isset($data['ticket_public_id']))) {
                    $data['type'] = 'ticket_reply';
                }

                if (!isset($data['link'])) {
                    $publicId = $data['ticket_public_id'] ?? null;
                    if (!$publicId && isset($data['ticket_id'])) {
                        $publicId = $publicIdsByTicketId[(int) $data['ticket_id']] ?? null;
                    }
                    if ($publicId) {
                        $data['ticket_public_id'] = $publicId;
                        $data['link'] = route(($user->role ?? null) === 'developer' ? 'developer.tickets.show' : 'tickets.show', $publicId);
                    }
                }

                $n->data = $data;
            }
        }

        self::$cached = [
            'unreadNotificationsCount' => $count,
            'unreadNotifications' => $items,
        ];

        $view->with(self::$cached);
    }
}
