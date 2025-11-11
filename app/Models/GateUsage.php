<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GateUsage extends Model
{
    use HasFactory;

    protected $table = 'gate_usage';

    protected $fillable = [
        'gate',
        'noshipment',
        'delivery_date',
        'timestart',
        'timeend',
    ];

    public function gate()
    {
        return $this->belongsTo(Gate::class, 'gate');
    }

    // Relasi opsional jika dibutuhkan
    // public function gateRef()
    // {
    //     return $this->belongsTo(Gate::class, 'gate', 'gate');
    // }

    // public function shipment()
    // {
    //     return $this->belongsTo(Shipment::class, 'noshipment', 'noshipment');
    // }
}
