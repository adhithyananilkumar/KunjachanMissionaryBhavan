<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RehabilitationPlan extends Model
{
    use HasFactory;
    protected $fillable = ['inmate_id','primary_issue','program_phase','goals'];
    public function inmate(){ return $this->belongsTo(Inmate::class); }
}
