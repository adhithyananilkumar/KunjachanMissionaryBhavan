<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo as EloquentBelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id', 'block_id', 'type', 'number', 'capacity', 'status'
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(LocationAssignment::class);
    }

    /**
     * All inmates attached to this location via assignments (historical and current).
     */
    public function inmates()
    {
        return $this->belongsToMany(Inmate::class, 'location_assignments', 'location_id', 'inmate_id')
            ->withPivot(['start_date','end_date'])
            ->withTimestamps();
    }

    // Convenience: currently active assignment (no end_date)
    public function activeAssignment(): ?LocationAssignment
    {
        if ($this->relationLoaded('assignments')) {
            return $this->assignments->firstWhere('end_date', null);
        }
        return $this->assignments()->whereNull('end_date')->latest('start_date')->first();
    }

    public function block(): EloquentBelongsTo
    {
        return $this->belongsTo(Block::class);
    }

    // Computed name: "{block prefix or name} - {Type} {number}"
    public function getNameAttribute(): string
    {
        $prefix = $this->block?->prefix ?: $this->block?->name ?: 'Block';
        return trim($prefix.' - '.ucfirst($this->type).' '.$this->number);
    }

    // Computed status reflecting occupancy
    public function getComputedStatusAttribute(): string
    {
        $occupied = $this->relationLoaded('assignments')
            ? $this->assignments->where('end_date', null)->isNotEmpty()
            : $this->assignments()->whereNull('end_date')->exists();
        return $occupied ? 'occupied' : ($this->status ?? 'available');
    }
}
