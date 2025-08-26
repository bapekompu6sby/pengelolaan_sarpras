<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailKamarTransaction extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'detail_kamar_transaction';

    // Kolom yang boleh diisi
    protected $fillable = [
        'transaction_id',
        'kamar_id',
        'start',
        'end',
    ];

    // Relasi ke Transaction
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    // Relasi ke Kamar
    
    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }
}
