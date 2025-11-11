<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;
    protected $table = 'vendors';
    protected $primaryKey = 'idvendor';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'idvendor',
        'transporter_name',
        'notelp',
        'address',
        'npwp',
        'created_by',
        'updated_by',
        'last_update',
    ];

    public function trucks()
    {
        return $this->hasMany(Truck::class, 'idvendor', 'idvendor');
    }
}
