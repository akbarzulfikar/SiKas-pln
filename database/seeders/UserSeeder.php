<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // Admin user
            [
                'user_id' => 'USR_UP3LGS_001',
                'name' => 'Administrator Sistem',
                'username' => 'admin',
                'email' => 'admin@account.co.id',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'unit_id' => 'up3_lgs',
                'nip' => 'ADM001',
                'position' => 'Administrator Sistem',
                'email_verified_at' => now(),
            ],
            // UP3 Manager
            [
                'user_id' => 'USR_UP3LGS_002',
                'name' => 'Manager UP3 Langsa',
                'username' => 'up3.langsa',
                'email' => 'manager.up3langsa@account.co.id',
                'password' => Hash::make('up3123'),
                'role' => 'user',
                'unit_id' => 'up3_lgs',
                'nip' => 'UP3001',
                'position' => 'Manager',
            ],
            // ULP Users
            [
                'user_id' => 'USR_ULPLKT_001',
                'name' => 'Supervisor ULP Langsa Kota',
                'username' => 'ulp.langsakota',
                'email' => 'supervisor.ulplangsakt@account.co.id',
                'password' => Hash::make('ulp123'),
                'role' => 'user',
                'unit_id' => 'ulp_lkt',
                'nip' => 'ULP001',
                'position' => 'Supervisor',
            ],
            [
                'user_id' => 'USR_ULPKSP_001',
                'name' => 'Supervisor ULP Kuala Simpang',
                'username' => 'ulp.kualasimpang',
                'email' => 'supervisor.ulpkualasimpang@account.co.id',
                'password' => Hash::make('ulp123'),
                'role' => 'user',
                'unit_id' => 'ulp_ksp',
                'nip' => 'ULP002',
                'position' => 'Supervisor',
            ],
            [
                'user_id' => 'USR_ULPPLK_001',
                'name' => 'Supervisor ULP Peureulak',
                'username' => 'ulp.peureulak',
                'email' => 'supervisor.ulppeureulak@account.co.id',
                'password' => Hash::make('ulp123'),
                'role' => 'user',
                'unit_id' => 'ulp_plk',
                'nip' => 'ULP003',
                'position' => 'Supervisor',
            ],
            [
                'user_id' => 'USR_ULPIDI_001',
                'name' => 'Supervisor ULP Idi',
                'username' => 'ulp.idi',
                'email' => 'supervisor.ulpidi@account.co.id',
                'password' => Hash::make('ulp123'),
                'role' => 'user',
                'unit_id' => 'ulp_idi',
                'nip' => 'ULP004',
                'position' => 'Supervisor',
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}