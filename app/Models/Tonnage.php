<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tonnage extends Model
{
    use HasFactory;

    protected $table = 'tonnages';
    protected $primaryKey = 'id';
    public $incrementing = false; // karena primary key string
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'type_truck',
        'desc',
        'created_by',
        'update_by',
        'last_update',
    ];

    /**
     * Generate otomatis ID seperti T1, T2, T3
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $last = Tonnage::orderBy('id', 'desc')->first();
                if ($last) {
                    $num = (int) substr($last->id, 1) + 1;
                } else {
                    $num = 1; // mulai dari T1
                }
                $model->id = 'T' . $num;
            }
        });
    }
}
