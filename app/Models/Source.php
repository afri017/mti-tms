<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use HasFactory;

    protected $table = 'sources';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'type',
        'location_name',
        'capacity',
        'created_by',
        'update_by',
        'last_update',
    ];

    protected $dates = ['last_update']; // agar otomatis jadi instance Carbon

    /**
     * Generate ID otomatis berdasarkan type
     * Source  => P00001
     * Destination => Q00001
     */
    public static function generateId($type)
    {
        $prefix = strtoupper(substr($type, 0, 1));
        if ($prefix === 'S') $prefix = 'P'; // Source
        if ($prefix === 'D') $prefix = 'Q'; // Destination

        $last = self::where('id', 'LIKE', "$prefix%")->orderBy('id', 'desc')->first();

        $num = 1;
        if ($last && preg_match('/\d+$/', $last->id, $matches)) {
            $num = intval($matches[0]) + 1;
        }

        return sprintf("%s%05d", $prefix, $num);
    }
}
