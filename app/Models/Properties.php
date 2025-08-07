<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Properties extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'capacity'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'property_id');
    }
}
