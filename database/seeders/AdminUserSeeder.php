<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Administrador Principal do Sistema
        DB::table('users')->updateOrInsert(
            ['email' => 'rayhenrique@gmail.com'],
            [
                'name' => 'Ray Henrique',
                'password' => Hash::make('1508rcrc'),
                'role' => 'admin',
                'status_ativo' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Administrador Institucional Padrão
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@ceodigital.gov.br'],
            [
                'name' => 'Administrador CEO',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status_ativo' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
