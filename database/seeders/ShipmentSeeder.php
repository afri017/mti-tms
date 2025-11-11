<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShipmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('shipments')->insert([
            'id' => 1,
            'noshipment' => '1100000001',
            'route' => 'R00001',
            'shipcost' => 'SC00001',
            'truck_id' => 'P00001',
            'driver' => 'A1001',
            'transporter' => 'TR001',
            'noseal' => null,
            'delivery_date' => '2025-10-21',
            'gate' => 'G1',
            'timestart' => '06:00:00',
            'timeend' => '06:15:00',
            'status' => 'Open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
