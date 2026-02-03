<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuardianMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'guardian_id',
        'message_text',
        'sent_by_guardian', // boolean: true if from guardian, false if from admin/system
        'read_at',
    ];

    protected $casts = [
        'sent_by_guardian' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }
}
