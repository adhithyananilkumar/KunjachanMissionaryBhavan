<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaseLogEntry extends Model
{
    use HasFactory;
    protected $fillable = ['inmate_id','entry_date','entry_text','user_id'];
    protected $casts = ['entry_date'=>'date'];
    public function inmate(){ return $this->belongsTo(Inmate::class); }
    public function user(){ return $this->belongsTo(User::class); }
}
