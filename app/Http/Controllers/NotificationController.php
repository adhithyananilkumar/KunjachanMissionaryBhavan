<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        // Load unread first, then recent read (limit)
        $unread = $user->unreadNotifications()->orderBy('created_at','desc')->get();
        $recentRead = $user->notifications()->whereNotNull('read_at')->orderBy('created_at','desc')->limit(30)->get();
        return view('notifications.index', compact('unread','recentRead'));
    }

    public function feed(Request $request)
    {
        $user = $request->user();
        $since = $request->query('since');
        $unreadOnly = (bool) $request->query('unread_only');
        $q = $unreadOnly ? $user->unreadNotifications() : $user->notifications();
        if ($since) {
            try { $dt = Carbon::parse($since); $q->where('created_at','>',$dt); } catch (\Throwable $e) {}
        }
        $items = $q->orderBy('created_at','desc')->limit(50)->get()->map(function($n){
            $data = $n->data ?? [];
            $type = $data['type'] ?? '';
            $title = match($type){
                'lab_test_ordered' => 'Lab Test Ordered',
                'lab_result_uploaded' => 'Lab Result Ready',
                'lab_result_rejected' => 'Lab Result Rejected',
                'transfer_of_care' => 'Transfer of Care',
                'emergency_appointment' => 'Emergency Appointment',
                'inmate_birthday' => 'Inmate Birthday',
                default => 'Notification',
            };
            $message = $data['message'] ?? ($data['test_name'] ?? ($data['title'] ?? ''));
            return [
                'id' => $n->id,
                'type' => $type ?: null,
                'title' => $title,
                'message' => $message,
                'link' => $data['link'] ?? ($data['url'] ?? null),
                'created_at' => $n->created_at?->toIso8601String(),
                'read' => (bool) $n->read_at,
            ];
        });
        return response()->json(['items' => $items]);
    }

    public function markAllRead(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();
        return response()->json(['ok'=>true]);
    }
}
