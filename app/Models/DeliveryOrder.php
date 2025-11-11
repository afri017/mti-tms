<?php

namespace App\Models;
use App\Models\Route;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    use HasFactory;

    protected $table = 'delivery_orders';

    protected $fillable = [
        'noshipment',
        'nodo',
        'nopo',
        'idtruck',         // ✅ Tambahan kolom truck
        'delivery_date',
        'source',
        'destination',
        'tara_weight',
        'gross_weight',
        'checkin',
        'checkout',
        'start_loading',
        'end_loading',
        'receipt_date',
        'created_by',
        'update_by',
        'last_update',
    ];

    protected $dates = [
        'delivery_date',
        'checkin',
        'checkout',
        'start_loading',
        'end_loading',
        'receipt_date',
        'last_update',
    ];

    public function items()
    {
        return $this->hasMany(DeliveryOrderItem::class, 'nodo', 'nodo');
    }

    public function truck()
    {
        return $this->belongsTo(\App\Models\Truck::class, 'idtruck', 'idtruck');
    }

    public function poheader()
    {
        return $this->belongsTo(\App\Models\PurchaseOrder::class, 'nopo', 'nopo');
    }

    public function sourceLocation()
    {
        return $this->belongsTo(\App\Models\source::class, 'source', 'id');
    }

    public function destinationLocation()
    {
        return $this->belongsTo(\App\Models\source::class, 'destination', 'id');
    }

    public function getRouteDataAttribute()
    {
        return Route::whereRaw("
            CONCAT(source, destination) = CONCAT(?, ?)
        ", [$this->source, $this->destination])->first();
    }

    public function attachments()
    {
        return $this->hasMany(\App\Models\DeliveryOrderAttachment::class, 'nodo', 'nodo');
    }

}
