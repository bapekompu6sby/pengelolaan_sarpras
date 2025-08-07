<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wisma extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'from',
        'room',
        'kegiatan',
        'isOut',
        'start',
        'end',
    ];
}
