<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InmatePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'inmate_id',
        'institution_id',
        'amount',
        'currency',
        'payment_date',
        'period_label',
        'status',
        'method',
        'reference',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function inmate()
    {
        return $this->belongsTo(Inmate::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
