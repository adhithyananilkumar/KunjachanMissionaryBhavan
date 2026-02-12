<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InmateDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'inmate_id',
        'document_name',
        'file_path',
        'is_sharable_with_guardian',
    ];

    protected $casts = [
        'is_sharable_with_guardian' => 'boolean',
    ];

    public function inmate(){
        return $this->belongsTo(Inmate::class);
    }
}
