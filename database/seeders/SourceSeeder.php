<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Source;

class SourceSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'id' => 'P00001',
                'type' => 'Source',
                'location_name' => 'Terminal Teluk Lamong',
                'capacity' => 10000,
                'created_by' => 'system',
                'last_update' => now(),
            ],
            [
                'id' => 'Q00001',
                'type' => 'Destination',
                'location_name' => 'Storage Bangkalan',
                'capacity' => 2000,
                'created_by' => 'system',
                'last_update' => now(),
            ],
            [
                'id' => 'Q00002',
                'type' => 'Destination',
                'location_name' => 'Storage Gresik',
                'capacity' => 2000,
                'created_by' => 'system',
                'last_update' => now(),
            ],
        ];

        foreach ($data as $item) {
            Source::updateOrCreate(['id' => $item['id']], $item);
        }
    }
}
