<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TherapySessionLog extends Model
{
    use HasFactory;
    protected $fillable = ['inmate_id','doctor_id','session_date','session_notes'];
    protected $casts = ['session_date'=>'datetime'];
    public function inmate(){ return $this->belongsTo(Inmate::class); }
    public function doctor(){ return $this->belongsTo(User::class,'doctor_id'); }
}
