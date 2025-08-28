<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penghuni extends Model
{
    use HasFactory;

    protected $table = 'penghuni';

    protected $fillable = [
        'detail_kamar_transaction_id',
        'nama_penghuni',
    ];

    public function detail()
{
    return $this->belongsTo(DetailKamarTransaction::class, 'detail_kamar_transaction_id');
}
}
