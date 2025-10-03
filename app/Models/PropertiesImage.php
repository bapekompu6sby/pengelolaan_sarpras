<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertiesImage extends Model
{
    protected $table = 'properties_image';

    protected $fillable = [
        'property_id',   // FK ke properties.id
        'image_path',    // simpan NAMA file (hash) saja
    ];

    public function property()
    {
        // relasi balik ke model Properties
        return $this->belongsTo(Properties::class, 'property_id');
    }
}
