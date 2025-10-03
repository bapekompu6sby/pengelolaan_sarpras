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
        'status',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'property_id');
    }

    public function kamar()
    {
        // biarkan sesuai skema kamu yang sekarang
        return $this->hasMany(Kamar::class, 'properties_id', 'id');
    }

    // ✅ Tambahkan ini: relasi 1..N ke tabel properties_image
    public function images()
    {
        return $this->hasMany(PropertiesImage::class, 'property_id', 'id')
            ->orderBy('id', 'asc');
    }

    
}
