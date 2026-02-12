<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    protected $fillable = ['image_path', 'caption', 'institution_id'];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
