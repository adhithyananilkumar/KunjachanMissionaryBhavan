<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'breakfast_amount',
        'lunch_amount',
        'dinner_amount',
        'other_amount',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
}
