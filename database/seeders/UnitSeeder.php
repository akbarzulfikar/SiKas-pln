<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            [
                'unit_id' => 'up3_lgs',
                'unit_name' => 'UP3 Langsa',
                'unit_type' => 'UP3',
                'address' => 'Jl. Merdeka No. 123, Langsa',
                'phone' => '0641-234567',
                'email' => 'up3langsa@pln.co.id',
            ],
            [
                'unit_id' => 'ulp_lkt',
                'unit_name' => 'ULP Langsa Kota',
                'unit_type' => 'ULP',
                'address' => 'Jl. Jend. Sudirman No. 45, Langsa',
                'phone' => '0641-234568',
                'email' => 'ulplangsakt@pln.co.id',
            ],
            [
                'unit_id' => 'ulp_ksp',
                'unit_name' => 'ULP Kuala Simpang',
                'unit_type' => 'ULP',
                'address' => 'Jl. Lintas Timur No. 78, Kuala Simpang',
                'phone' => '0641-345678',
                'email' => 'ulpkualasimpang@pln.co.id',
            ],
            [
                'unit_id' => 'ulp_plk',
                'unit_name' => 'ULP Peureulak',
                'unit_type' => 'ULP',
                'address' => 'Jl. Banda Aceh-Medan KM 45, Peureulak',
                'phone' => '0641-456789',
                'email' => 'ulppeureulak@pln.co.id',
            ],
            [
                'unit_id' => 'ulp_idi',
                'unit_name' => 'ULP Idi',
                'unit_type' => 'ULP',
                'address' => 'Jl. Cut Nyak Dien No. 12, Idi',
                'phone' => '0641-567890',
                'email' => 'ulpidi@pln.co.id',
            ]
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}