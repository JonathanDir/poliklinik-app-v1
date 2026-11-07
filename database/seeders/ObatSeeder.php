<?php

namespace Database\Seeders;

use App\Models\Obat;
use Illuminate\Database\Seeder;

class ObatSeeder extends Seeder
{
    public function run(): void
    {
        $obats = [
            ['Paracetamol 500 mg', 'Strip isi 10 kaplet', 4000],
            ['Ibuprofen 400 mg', 'Strip isi 10 tablet', 6100],
            ['Cetirizine 10 mg', 'Strip isi 10 tablet', 6700],
            ['Amoxicillin 500 mg', 'Strip isi 10 tablet', 8200],
            ['Omeprazole 20 mg', 'Strip isi 10 kapsul', 8300],
            ['Vitamin C 500 mg', 'Strip isi 10 tablet', 8300],
            ['Ambroxol 30 mg', 'Strip isi 10 tablet', 4300],
            ['Antasida Doen', 'Strip isi 10 tablet', 2600],
            ['Povidone Iodine Solution 30 ml', 'Botol 30 ml', 2800],
            ['Salbutamol 2 mg', 'Strip isi 10 tablet', 2600],
            ['Asam Mefenamat 500 mg', 'Strip isi 10 kaplet', 4700],
            ['Oralit 200 4.1 g', 'Sachet untuk 200 ml air', 1100],
        ];

        foreach ($obats as [$nama, $kemasan, $harga]) {
            Obat::firstOrCreate(
                ['nama_obat' => $nama],
                ['kemasan' => $kemasan, 'harga' => $harga],
            );
        }
    }
}
