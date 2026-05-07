<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'inmate_id','medical_record_id','name','dosage','route','frequency','start_date','end_date','status','instructions'
    ];

    public function inmate(){ return $this->belongsTo(Inmate::class); }
    public function medicalRecord(){ return $this->belongsTo(MedicalRecord::class); }
}
