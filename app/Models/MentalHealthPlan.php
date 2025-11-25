<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MentalHealthPlan extends Model
{
    use HasFactory;
    protected $fillable = ['inmate_id','diagnosis','therapy_frequency','current_meds'];
    public function inmate(){ return $this->belongsTo(Inmate::class); }
}
