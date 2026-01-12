<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BugReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description', 'screenshot_path', 'status', 'developer_reply', 'developer_attachment_path', 'reply_seen'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
