<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ride_order_id',
        'amount',
        'payment_method',
        'paid_at',
    ];

    public function rideOrder()
    {
        return $this->belongsTo(RideOrder::class);
    }
}
