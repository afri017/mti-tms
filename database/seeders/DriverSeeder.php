<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Driver;
use Carbon\Carbon;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = [
            ['iddriver'=>'A1001','name'=>'Ahmad Fauzi','no_sim'=>'SIM-FKT-1002345678','typesim'=>'B2','notelp'=>'6281234567890','address'=>'Jl. Jendral Sudirman No. 45, Kel. Karet Tengsin, Kec. Tanah Abang, Kota Jakarta Pusat, DKI Jakarta 10220'],
            ['iddriver'=>'A1002','name'=>'Budi Santoso','no_sim'=>'SIM-FKT-1003456789','typesim'=>'B2','notelp'=>'6281398765432','address'=>'Jl. Diponegoro No. 12, Kel. Citarum, Kec. Bandung Wetan, Kota Bandung, Jawa Barat 40115'],
            ['iddriver'=>'A1003','name'=>'Cahyo Prasetya','no_sim'=>'SIM-FKT-1004567890','typesim'=>'B2','notelp'=>'6282155667788','address'=>'Jl. Pahlawan No. 88, Kel. Tambak Sari, Kec. Gayungan, Kota Surabaya, Jawa Timur 60235'],
            ['iddriver'=>'A1004','name'=>'Dedi Wiranto','no_sim'=>'SIM-FKT-1005678901','typesim'=>'B2','notelp'=>'6282233449900','address'=>'Jl. Gatot Subroto No. 25, Kel. Sudirman, Kec. Denpasar Barat, Kota Denpasar, Bali 80223'],
            ['iddriver'=>'A1005','name'=>'Eka Nugroho','no_sim'=>'SIM-FKT-1006789012','typesim'=>'B2','notelp'=>'6285211223344','address'=>'Jl. Ahmad Yani No. 101, Kel. Sukaramai, Kec. Medan Area, Kota Medan, Sumatera Utara 20234'],
            ['iddriver'=>'A1006','name'=>'Fajar Hidayat','no_sim'=>'SIM-FKT-1007890123','typesim'=>'B2','notelp'=>'6285366778899','address'=>'Jl. Dr. Wahidin No. 56, Kel. Panunggangan Barat, Kec. Cibodas, Kota Tangerang, Banten 15138'],
            ['iddriver'=>'A1007','name'=>'Guntur Maulana','no_sim'=>'SIM-FKT-1008901234','typesim'=>'B2','notelp'=>'6285544332211','address'=>'Jl. Ahmad Dahlan No. 27, Kel. Karangasem, Kec. Laweyan, Kota Surakarta, Jawa Tengah 57145'],
            ['iddriver'=>'A1008','name'=>'Hendrianto','no_sim'=>'SIM-FKT-1009012345','typesim'=>'B2','notelp'=>'6287722334455','address'=>'Jl. Sultan Hasanuddin No. 89, Kel. Rappocini, Kec. Rappocini, Kota Makassar, Sulawesi Selatan 90222'],
            ['iddriver'=>'A1009','name'=>'Irwan Setiawan','no_sim'=>'SIM-FKT-1010123456','typesim'=>'B2','notelp'=>'6289677889900','address'=>'Jl. Imam Bonjol No. 34, Kel. Pahandut, Kec. Pahandut, Kota Palangka Raya, Kalimantan Tengah 73111'],
            ['iddriver'=>'A1010','name'=>'Joko Prabowo','no_sim'=>'SIM-FKT-1011234567','typesim'=>'B2','notelp'=>'6289533445566','address'=>'Jl. Sam Ratulangi No. 14, Kel. Wenang Selatan, Kec. Wenang, Kota Manado, Sulawesi Utara 95111'],
        ];

        foreach($drivers as $d){
            Driver::create(array_merge($d, [
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ]));
        }
    }
}
