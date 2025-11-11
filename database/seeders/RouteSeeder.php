<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Route;
use App\Models\Source;

class RouteSeeder extends Seeder
{
    public function run(): void
    {
        $source1 = Source::find('P00001');
        $dest1 = Source::find('Q00001');
        $dest2 = Source::find('Q00002');

        Route::create([
            'route' => 'R00001',
            'source' => 'P00001',
            'destination' => 'Q00001',
            'leadtime' => 1,
            'route_name' => "{$source1->location_name} - {$dest1->location_name}",
        ]);

        Route::create([
            'route' => 'R00002',
            'source' => 'P00001',
            'destination' => 'Q00002',
            'leadtime' => 1,
            'route_name' => "{$source1->location_name} - {$dest2->location_name}",
        ]);
    }
}
