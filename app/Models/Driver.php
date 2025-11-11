<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $table = 'drivers';
    protected $primaryKey = 'iddriver';
    public $incrementing = false; // karena string
    protected $keyType = 'string';

    protected $fillable = [
        'iddriver',
        'name',
        'no_sim',
        'typesim',
        'notelp',
        'address',
        'created_by',
        'update_by',
        'last_update',
    ];

    /**
     * Generate otomatis ID driver seperti A1001, A1002, dst
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->iddriver) {
                // Ambil driver terakhir
                $last = Driver::orderBy('iddriver', 'desc')->first();
                if ($last) {
                    $num = (int) substr($last->iddriver, 1) + 1; // ambil angka setelah A
                } else {
                    $num = 1001; // mulai dari A1001
                }
                $model->iddriver = 'A' . $num;
            }
        });
    }
}
