<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'car_model',
        'license_plate',
        'phone',
    ];

    public function rideOrders()
    {
        return $this->hasMany(RideOrder::class);
    }
}