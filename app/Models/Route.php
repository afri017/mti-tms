<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $table = 'routes';
    protected $primaryKey = 'route';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'route',
        'source',
        'destination',
        'leadtime',
        'route_name',
        'created_by',
        'update_by',
        'last_update',
    ];

    public static function generateRouteId()
    {
        $last = self::orderBy('route', 'desc')->first();
        $num = $last ? intval(substr($last->route, 1)) + 1 : 1;
        return sprintf("R%05d", $num);
    }

    public function sourceData()
    {
        return $this->belongsTo(Source::class, 'source', 'id');
    }

    public function destinationData()
    {
        return $this->belongsTo(Source::class, 'destination', 'id');
    }

    /**
     * Set route_name otomatis berdasarkan lokasi source dan destination.
     */
    protected static function booted()
    {
        static::creating(function ($route) {
            $source = \App\Models\Source::find($route->source);
            $destination = \App\Models\Source::find($route->destination);

            if ($source && $destination) {
                $route->route_name = "{$source->location_name} - {$destination->location_name}";
            }

            if (empty($route->route)) {
                $route->route = self::generateRouteId();
            }
        });

        static::updating(function ($route) {
            $source = \App\Models\Source::find($route->source);
            $destination = \App\Models\Source::find($route->destination);

            if ($source && $destination) {
                $route->route_name = "{$source->location_name} - {$destination->location_name}";
            }
        });
    }
}
