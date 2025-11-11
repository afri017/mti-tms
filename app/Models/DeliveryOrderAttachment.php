<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrderAttachment extends Model
{
    use HasFactory;

    protected $table = 'delivery_order_attachments';

    protected $fillable = [
        'nodo',
        'filename',
        'filepath',
        'uploaded_by',
    ];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class, 'nodo', 'nodo');
    }

    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        return asset($this->filepath);
    }
}
