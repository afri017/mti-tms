<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Truck;
use Carbon\Carbon;

class TruckSeeder extends Seeder
{
    public function run(): void
    {
        $trucks = [
            ['idtruck'=>'P00001','idvendor'=>'TR001','iddriver'=>'A1001','type_truck'=>'T1','stnk'=>'STNK-FKT-TRK-0001','merk'=>'Hino','nopol'=>'B9567FAA','expired_kir'=>'2025-10-11'],
            ['idtruck'=>'P00002','idvendor'=>'TR001','iddriver'=>'A1002','type_truck'=>'T1','stnk'=>'STNK-FKT-TRK-0002','merk'=>'Hino','nopol'=>'B9566FAB','expired_kir'=>'2025-11-11'],
            ['idtruck'=>'P00003','idvendor'=>'TR001','iddriver'=>'A1003','type_truck'=>'T1','stnk'=>'STNK-FKT-TRK-0003','merk'=>'Hino','nopol'=>'B9565FAC','expired_kir'=>'2025-12-11'],
            ['idtruck'=>'P00004','idvendor'=>'TR001','iddriver'=>'A1004','type_truck'=>'T1','stnk'=>'STNK-FKT-TRK-0004','merk'=>'Hino','nopol'=>'B9564FAD','expired_kir'=>'2025-11-13'],
            ['idtruck'=>'P00005','idvendor'=>'TR001','iddriver'=>'A1005','type_truck'=>'T1','stnk'=>'STNK-FKT-TRK-0005','merk'=>'Hino','nopol'=>'B9563FAE','expired_kir'=>'2025-11-14'],
            ['idtruck'=>'P00006','idvendor'=>'TR001','iddriver'=>'A1006','type_truck'=>'T2','stnk'=>'STNK-FKT-TRK-0006','merk'=>'Hino','nopol'=>'B9562FAF','expired_kir'=>'2025-11-15'],
            ['idtruck'=>'P00007','idvendor'=>'TR001','iddriver'=>'A1007','type_truck'=>'T2','stnk'=>'STNK-FKT-TRK-0007','merk'=>'Hino','nopol'=>'B9561FAG','expired_kir'=>'2025-11-16'],
            ['idtruck'=>'P00008','idvendor'=>'TR001','iddriver'=>'A1008','type_truck'=>'T2','stnk'=>'STNK-FKT-TRK-0008','merk'=>'Hino','nopol'=>'B9568FAH','expired_kir'=>'2025-11-17'],
            ['idtruck'=>'P00009','idvendor'=>'TR001','iddriver'=>'A1009','type_truck'=>'T2','stnk'=>'STNK-FKT-TRK-0009','merk'=>'Hino','nopol'=>'B9569FAI','expired_kir'=>'2025-11-18'],
            ['idtruck'=>'P00010','idvendor'=>'TR001','iddriver'=>'A1010','type_truck'=>'T2','stnk'=>'STNK-FKT-TRK-0010','merk'=>'Hino','nopol'=>'B9560FAJ','expired_kir'=>'2025-11-19'],
        ];

        foreach($trucks as $t){
            Truck::create(array_merge($t, [
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ]));
        }
    }
}
