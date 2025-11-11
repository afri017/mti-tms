<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrderItem extends Model
{
    use HasFactory;

    protected $table = 'delivery_order_items';

    protected $fillable = [
        'nodo',
        'doitem',
        'material_code',
        'qty_plan',
        'qty_act',
        'uom',
        'qty_receipt',
        'qty_reject',
        'created_by',
        'update_by',
        'last_update',
    ];

    protected $dates = ['last_update'];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class, 'nodo', 'nodo');
    }

    public function material()
    {
        return $this->belongsTo(\App\Models\Material::class, 'material_code', 'material_code');
    }
}
