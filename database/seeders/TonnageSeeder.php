<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tonnage;
use Carbon\Carbon;

class TonnageSeeder extends Seeder
{
    public function run(): void
    {
        $tonnages = [
            ['id'=>'T1','type_truck'=>20,'desc'=>'20 Ton'],
            ['id'=>'T2','type_truck'=>30,'desc'=>'30 Ton'],
        ];

        foreach($tonnages as $t){
            Tonnage::create(array_merge($t, [
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ]));
        }
    }
}
