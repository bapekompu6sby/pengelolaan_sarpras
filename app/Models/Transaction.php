<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'instansi',
        'kegiatan',
        'start',
        'end',
        'color',
        'property_id',
        'status',
        'description',
        'user_id'
    ];

    public function properties()
    {
        return $this->belongsTo(Properties::class, 'property_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
