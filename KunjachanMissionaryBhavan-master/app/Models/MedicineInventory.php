<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicineInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id','medicine_id','quantity','threshold','updated_by'
    ];

    public function institution(): BelongsTo { return $this->belongsTo(Institution::class); }
    public function medicine(): BelongsTo { return $this->belongsTo(Medicine::class); }
    public function updatedBy(): BelongsTo { return $this->belongsTo(User::class,'updated_by'); }
}
