<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DoctorHandoff extends Model
{
    use HasFactory;
    protected $fillable = ['inmate_id','from_doctor_id','to_doctor_id','admin_id','reason'];
    public function inmate(){ return $this->belongsTo(Inmate::class); }
    public function fromDoctor(){ return $this->belongsTo(User::class,'from_doctor_id'); }
    public function toDoctor(){ return $this->belongsTo(User::class,'to_doctor_id'); }
    public function admin(){ return $this->belongsTo(User::class,'admin_id'); }
}
