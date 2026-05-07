<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examination extends Model
{
    use HasFactory;

    protected $fillable = [
        'inmate_id','created_by','creator_role','title','notes','severity','observed_at'
    ];

    protected $casts = [
        'observed_at' => 'datetime',
    ];

    public function inmate(){ return $this->belongsTo(Inmate::class); }
    public function creator(){ return $this->belongsTo(User::class,'created_by'); }
}
