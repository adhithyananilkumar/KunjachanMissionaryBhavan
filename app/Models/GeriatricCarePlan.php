<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GeriatricCarePlan extends Model
{
    use HasFactory;
    protected $fillable = ['inmate_id','mobility_status','dietary_needs','emergency_contact_details'];
    protected $casts = ['emergency_contact_details'=>'array'];
    public function inmate(){ return $this->belongsTo(Inmate::class); }
}
