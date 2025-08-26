<?php

namespace App\Models;

use App\Models\Properties;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kamar extends Model
{
    use HasFactory;

    protected $table = 'kamar';

    protected $fillable = [
        'penginapan_id',
        'nama_kamar',
        'kapasitas',
        
        
    ];


    // Relasi ke Ruangan (penginapan)
    public function ruangan()
    {
        return $this->belongsTo(Properties::class, 'penginapan_id');
    }
    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, 'transaction_kamar')
            ->withPivot(['start', 'end', 'nama_penghuni'])
            ->withTimestamps();
    }
}
