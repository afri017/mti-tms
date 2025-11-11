<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('vendors')->insert([
            [
                'idvendor' => 'TR001',
                'transporter_name' => 'PT Nusantara Logistik Sejahtera',
                'notelp' => '0812-3344-5566',
                'address' => 'Jl. Raya Cakung No. 88, Jakarta Timur, DKI Jakarta',
                'npwp' => '01.234.567.8-901.000',
                'created_by' => 'system',
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'last_update' => now(),
            ],
            [
                'idvendor' => 'TR002',
                'transporter_name' => 'CV Maju Lancar Transindo',
                'notelp' => '0852-7788-9900',
                'address' => 'Jl. Soekarno Hatta No. 45, Bandung, Jawa Barat',
                'npwp' => '02.345.678.9-012.000',
                'created_by' => 'system',
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'last_update' => now(),
            ],
        ]);
    }
}
