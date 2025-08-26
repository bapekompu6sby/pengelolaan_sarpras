<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Properties extends Model
{
    use HasFactory;

    protected $table = 'properties';

    protected $fillable = [
        'name',
        'type',
        'capacity',
        'image_path',
        'room_type',
        'area',
        'facilities',
        'price',
        'unit',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'property_id');
    }

    public function kamar()
    {
        return $this->hasMany(Kamar::class, 'penginapan_id', 'id');
    }
    
}
