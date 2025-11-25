<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;
    protected $fillable = [
        'inmate_id','scheduled_by','scheduled_for','title','notes','status'
    ];
    protected $casts = [
        'scheduled_for'=>'datetime'
    ];
    public function inmate(){ return $this->belongsTo(Inmate::class); }
    public function doctor(){ return $this->belongsTo(User::class,'scheduled_by'); }
}
