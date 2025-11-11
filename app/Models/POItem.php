<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class POItem extends Model
{
    use HasFactory;

    protected $table = 'po_items';

    protected $fillable = [
        'nopo',
        'itempo',
        'material_code',
        'qty',
        'uom',
        'created_by',
        'update_by',
        'last_update'
    ];

    protected $dates = ['last_update'];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_code', 'material_code');
    }
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'nopo', 'nopo');
    }
}
