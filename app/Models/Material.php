<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $table = 'materials';
    protected $primaryKey = 'id';
    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'update_at';

    protected $fillable = [
        'material_code',
        'material_desc',
        'uom',
        'konversi_ton',
        'created_by',
        'update_by',
        'last_update',
    ];

    /**
     * Generate otomatis material_code: S-001, S-002, dst.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $latest = static::orderBy('id', 'desc')->first();
            $nextNumber = $latest ? ((int) substr($latest->material_code, -3)) + 1 : 1;
            $model->material_code = 'S-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        });
    }
}
