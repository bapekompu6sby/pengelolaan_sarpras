<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'instansi',
        'kegiatan',
        'start',
        'end',
        'color',
        'property_id',
        'status',
        'description'
    ];

    public function properties()
    {
        return $this->belongsTo(Properties::class, 'property_id');
    }
}
