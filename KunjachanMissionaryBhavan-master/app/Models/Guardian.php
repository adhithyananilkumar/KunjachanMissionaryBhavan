<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Guardian extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'phone_number',
        'address',
    ];

    public function inmate(): HasOne
    {
        return $this->hasOne(Inmate::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
