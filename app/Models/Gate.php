<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gate extends Model
{
    use HasFactory;

    protected $fillable = [
        'gate',
        'point',
        'timestart',
        'timeend',
        'type',
        'value',
    ];

    public function source()
    {
        return $this->belongsTo(Source::class, 'point', 'id');
    }

    public function gateUsage()
    {
        return $this->hasMany(GateUsage::class, 'gate');
    }
}
