<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationsStreamController extends Controller
{
    public function stream(Request $request)
    {
        $user = $request->user();
        abort_unless($user, 401);

        if (!config('notifications.sse_enabled', false)) {
            abort(404);
        }

        $since = $request->query('since');
        $sinceId = $request->header('Last-Event-ID');

        $maxSeconds = (int) config('notifications.sse_duration_seconds', 15);
        $sleepMs = (int) config('notifications.sse_sleep_ms', 1500);
        $sleepUs = max(250000, min(5000000, $sleepMs * 1000));

        return response()->stream(function () use ($user, $since, $sinceId, $maxSeconds, $sleepUs) {
            @ini_set('output_buffering', 'off');
            @ini_set('zlib.output_compression', '0');
            @set_time_limit(0);

            $start = microtime(true);
            $lastId = $sinceId ?: null;
            $lastCreatedAt = $since;

            while (microtime(true) - $start < $maxSeconds) {
                $q = $user->unreadNotifications()->orderBy('created_at', 'asc');
                if ($lastId) {
                    $q->where('id', '!=', $lastId);
                }
                if ($lastCreatedAt) {
                    try {
                        $q->where('created_at', '>', \Illuminate\Support\Carbon::parse($lastCreatedAt));
                    } catch (\Throwable $e) {
                    }
                }

                $items = $q->limit(10)->get();

                if ($items->isNotEmpty()) {
                    $payload = [
                        'unread_count' => $user->unreadNotifications()->count(),
                        'items' => $items->map(function ($n) {
                            return [
                                'id' => $n->id,
                                'data' => $n->data,
                                'created_at' => $n->created_at?->toIso8601String(),
                            ];
                        })->values(),
                    ];

                    $last = $items->last();
                    $lastId = $last->id;
                    $lastCreatedAt = $last->created_at?->toIso8601String();

                    echo "id: {$lastId}\n";
                    echo "event: notification\n";
                    echo 'data: ' . json_encode($payload) . "\n\n";
                    @ob_flush();
                    @flush();
                } else {
                    echo "event: ping\n";
                    echo 'data: ' . json_encode(['t' => now()->toIso8601String()]) . "\n\n";
                    @ob_flush();
                    @flush();
                }

                usleep($sleepUs);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-transform',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
