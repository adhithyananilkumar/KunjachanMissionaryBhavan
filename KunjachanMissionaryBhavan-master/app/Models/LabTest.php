<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'inmate_id','ordered_by','updated_by','test_name','status','ordered_date','completed_date','notes','result_notes','result_file_path','reviewed_at','reviewed_by'
    ];

    public function inmate(){ return $this->belongsTo(Inmate::class); }
    public function orderedBy(){ return $this->belongsTo(User::class,'ordered_by'); }
    public function updatedBy(){ return $this->belongsTo(User::class,'updated_by'); }
    public function reviewedBy(){ return $this->belongsTo(User::class,'reviewed_by'); }

    protected $casts = [
        'ordered_date' => 'datetime',
        'completed_date' => 'datetime',
    'reviewed_at' => 'datetime',
    ];
}
