<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    use HasFactory;

    protected $table = 'trucks';
    protected $primaryKey = 'idtruck';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'idtruck',
        'idvendor',
        'iddriver',
        'type_truck',
        'stnk',
        'merk',
        'nopol',
        'expired_kir',
        'created_by',
        'update_by',
        'last_update',
    ];

    /**
     * Relasi ke Driver
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'iddriver', 'iddriver');
    }

    /**
     * Relasi ke Tonnage
     */
    public function tonnage()
    {
        return $this->belongsTo(Tonnage::class, 'type_truck', 'id');
    }

    /**
     * Relasi ke Vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'idvendor', 'idvendor');
    }

    /**
     * Auto-generate ID truck seperti P00001, P00002
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->idtruck) {
                $last = Truck::orderBy('idtruck', 'desc')->first();
                if ($last) {
                    $num = (int) substr($last->idtruck, 1) + 1;
                } else {
                    $num = 1; // mulai dari P00001
                }
                $model->idtruck = 'P' . str_pad($num, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
