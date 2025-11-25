<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationalRecord extends Model
{
    use HasFactory;
    protected $fillable = ['inmate_id','school_name','grade','academic_year','notes'];
    protected $casts = [
        'subjects_and_grades' => 'array',
    ];
    public function inmate(){ return $this->belongsTo(Inmate::class); }
}
