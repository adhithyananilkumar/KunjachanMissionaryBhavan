<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SupportTicket extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'open';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_WAITING = 'waiting';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'public_id',
        'user_id',
        'assigned_to',
        'title',
        'description',
        'module',
        'severity',
        'environment',
        'page_url',
        'app_version',
        'deployment_tag',
        'fixed_in_version',
        'screenshot_path',
        'screenshot_paths',
        'status',
        'resolved_at',
        'resolution_summary',
        'close_reason',
        'closed_at',
        'archived_at',
        'user_last_seen_at',
        'developer_last_seen_at',
        'developer_reply',
        'developer_attachment_path',
        'reply_seen',
        'last_activity_at',
    ];

    protected $casts = [
        'reply_seen' => 'boolean',
        'last_activity_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'archived_at' => 'datetime',
        'user_last_seen_at' => 'datetime',
        'developer_last_seen_at' => 'datetime',
        'environment' => 'array',
        'screenshot_paths' => 'array',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function replies(): HasMany { return $this->hasMany(TicketReply::class); }
    public function activities(): HasMany { return $this->hasMany(SupportTicketActivity::class, 'support_ticket_id'); }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    protected static function booted(): void
    {
        static::creating(function (self $ticket) {
            if (!$ticket->public_id) {
                $ticket->public_id = 'TKT-' . (string) Str::ulid();
            }
        });
    }
}
