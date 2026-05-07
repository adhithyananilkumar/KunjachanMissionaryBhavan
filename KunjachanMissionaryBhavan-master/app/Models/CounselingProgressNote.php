<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CounselingProgressNote extends Model
{
    use HasFactory;
    protected $fillable = ['inmate_id','user_id','note_date','progress_assessment','milestones_achieved'];
    protected $casts = ['note_date'=>'datetime'];
    public function inmate(){ return $this->belongsTo(Inmate::class); }
    public function user(){ return $this->belongsTo(User::class); }
}
