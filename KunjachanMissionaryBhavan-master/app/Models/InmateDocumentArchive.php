<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InmateDocumentArchive extends Model
{
    use HasFactory;

    protected $fillable = [
        'inmate_id',
        'source_type',
        'source_key',
        'inmate_document_id',
        'document_name',
        'file_path',
        'archived_by',
        'archived_at',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
    ];

    public function inmate()
    {
        return $this->belongsTo(Inmate::class);
    }

    public function inmateDocument()
    {
        return $this->belongsTo(InmateDocument::class);
    }
}
