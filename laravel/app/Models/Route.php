<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_location',
        'end_location',
        'distance_km',
    ];

    public function rideOrders()
    {
        return $this->hasMany(RideOrder::class);
    }
}