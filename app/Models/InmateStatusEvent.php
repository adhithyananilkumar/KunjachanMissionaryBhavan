<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InmateStatusEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'inmate_id',
        'event_type',
        'from_status',
        'to_status',
        'effective_at',
        'reason',
        'meta',
        'attachments',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'effective_at' => 'datetime',
        'created_at' => 'datetime',
        'meta' => 'array',
        'attachments' => 'array',
    ];

    protected static function booted(): void
    {
        static::updating(function () {
            throw new \RuntimeException('Status events are immutable.');
        });

        static::deleting(function () {
            throw new \RuntimeException('Status events cannot be deleted.');
        });
    }

    public function inmate(): BelongsTo
    {
        return $this->belongsTo(Inmate::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
