<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentCost extends Model
{
    use HasFactory;

    protected $table = 'shipment_costs';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'idvendor',
        'route',
        'type_truck',
        'price_freight',
        'price_driver',
        'validity_start',
        'validity_end',
        'active',
    ];

    protected $casts = [
        'validity_start' => 'date',
        'validity_end' => 'date',
    ];

    // 🔗 Relasi ke Vendor
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'idvendor', 'idvendor');
    }

    // 🔗 Relasi ke Route
    public function routeData()
    {
        return $this->belongsTo(Route::class, 'route', 'route');
    }

    // 🔗 Relasi ke Tonnage / Truck Type
    public function truckType()
    {
        return $this->belongsTo(Tonnage::class, 'type_truck', 'id');
    }

    // 🧮 Auto-generate ID seperti SC00001
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $last = ShipmentCost::orderBy('id', 'desc')->first();
                $num = $last ? ((int) substr($last->id, 2)) + 1 : 1;
                $model->id = 'SC' . str_pad($num, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
