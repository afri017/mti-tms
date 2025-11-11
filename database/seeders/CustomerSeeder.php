<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Customer; // ← baris penting ini harus ada

class CustomerSeeder extends Seeder
{
    /**
     * Jalankan seeder.
     */
    public function run(): void
    {
        Customer::create([
            'customer_name' => 'PT FKS',
            'address' => 'Menara Astra Lantai 29, Jl. Jend. Sudirman Kav. 5-6, Karet Tengsin, Tanah Abang, Jakarta Pusat, DKI Jakarta 10220',
            'notelp' => '62215687299',
            'is_active' => 'Y',
            'created_by' => 'system',
            'update_by' => null,
            'last_update' => now(),
        ]);
    }
}
