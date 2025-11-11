<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';
    protected $primaryKey = 'id';
    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'update_at';

    protected $fillable = [
        'idcustomer',
        'customer_name',
        'address',
        'notelp',
        'is_active',
        'created_by',
        'update_by',
        'last_update',
    ];

    /**
     * Generate otomatis idcustomer sebelum menyimpan.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Ambil ID terakhir dari tabel
            $latest = static::orderBy('id', 'desc')->first();

            // Hitung nomor urut berikutnya
            $nextNumber = $latest ? ((int) substr($latest->idcustomer, -3)) + 1 : 1;

            // Format: cust_001, cust_002, dst
            $model->idcustomer = 'cu_' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }
}
