<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'purchase_orders';
    protected $primaryKey = 'id';
    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'update_at';

    protected $fillable = [
        'nopo',
        'idcustomer',
        'podate',
        'valid_to',
        'created_by',
        'update_by',
        'last_update',
    ];

    /**
     * Generate otomatis nomor PO dengan format: PO-YYYY-XXX
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $year = date('Y');

            // Ambil PO terakhir di tahun ini
            $latest = static::whereYear('podate', $year)
                ->orderBy('id', 'desc')
                ->first();

            $nextNumber = 1;
            if ($latest && preg_match('/PO-' . $year . '-(\d+)/', $latest->nopo, $match)) {
                $nextNumber = ((int)$match[1]) + 1;
            }

            $model->nopo = 'PO-' . $year . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Relasi ke Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'idcustomer', 'idcustomer');
    }

    public function poItems()
    {
        return $this->hasMany(POItem::class, 'nopo', 'nopo');
    }

}
