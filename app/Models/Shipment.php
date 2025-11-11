<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'noshipment',
        'route',
        'shipcost',
        'truck_id',
        'driver',
        'transporter',
        'noseal',
        'delivery_date',
        'gate',
        'timestart',
        'timeend',
        'status',
    ];

    // Relasi opsional
    public function truck()
    {
        return $this->belongsTo(Truck::class, 'truck_id', 'idtruck');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver', 'iddriver');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'transporter', 'idvendor');
    }

    public function shipmentCost()
    {
        return $this->belongsTo(ShipmentCost::class, 'shipcost', 'id');
    }

    public function routeData()
    {
        return $this->belongsTo(Route::class, 'route', 'route');
    }

    public function doship()
    {
        return $this->hasMany(DeliveryOrder::class, 'noshipment', 'noshipment');
    }
}
