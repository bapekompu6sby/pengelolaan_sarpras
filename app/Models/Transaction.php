<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTime;

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
        'payment_receipt',
        'request_letter',
        'description',
        'user_id',
        'affiliation',
        'phone_number',
        'email',
        'ordered_unit',
        'total_harga',
    ];

    protected static function booted()
    {
        static::created(function ($transaction) {
            // This runs right after the model is inserted
            $transaction->calculateTotalPrice();
        });
    }

    

    public function calculateTotalPrice()
    {
        // Your logic here
        // Example: log, notify, update another table, etc.
        $total = 0;
        if ($this->affiliation == 'internal_pu' || $this->end < $this->start){
            $total = 0;
        }
        else{
            $price = $this->properties->price;
            $st = new DateTime($this->start);
            $ed = new DateTime($this->end);
            $interval = $st->diff($ed);
            $duration = $interval->days + 1;
            $total = $price * $duration;
        }
        $this->total_harga = $total;
        $this->save();
        return true;
    }

    public function properties()
    {
        return $this->belongsTo(Properties::class, 'property_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
