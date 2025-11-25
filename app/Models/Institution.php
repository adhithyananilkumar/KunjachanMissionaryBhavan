<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int,string>
     */
    protected $fillable = [
    'name',
    'address',
    'phone',
    'email',
    'enabled_features',
    'doctor_assignment_enabled'
    ];

    protected $casts = [
        'enabled_features' => 'array',
        'doctor_assignment_enabled' => 'boolean',
    ];

    /**
     * Get the users for the institution.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    /**
     * Get the inmates for the institution.
     */
    public function inmates(): HasMany
    {
        return $this->hasMany(Inmate::class);
    }
}
