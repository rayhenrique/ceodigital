<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UbsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unidades = [
            [
                'nome' => 'UBS Hamilton Falcão',
                'endereco' => 'Av. Menino Marcelo, 1200 - Serraria',
                'diretor' => 'Dra. Mariana Albuquerque',
                'contato' => '(82) 3315-1001',
            ],
            [
                'nome' => 'UBS Maria Teresa Holanda',
                'endereco' => 'Rua do Sol, 45 - Centro',
                'diretor' => 'Dr. Carlos Eduardo Lima',
                'contato' => '(82) 3315-1002',
            ],
            [
                'nome' => 'UBS Roland Simon',
                'endereco' => 'Av. Fernandes Lima, 850 - Farol',
                'diretor' => 'Enf. Patrícia Gomes',
                'contato' => '(82) 3315-1003',
            ],
            [
                'nome' => 'UBS José Araújo Silva',
                'endereco' => 'Rua Comendador Palmeira, 310 - Jacintinho',
                'diretor' => 'Dr. Fernando Mendonça',
                'contato' => '(82) 3315-1004',
            ],
            [
                'nome' => 'UBS Pitanguinha',
                'endereco' => 'Rua Santo Antônio, 90 - Pitanguinha',
                'diretor' => 'Dra. Juliana Cavalcante',
                'contato' => '(82) 3315-1005',
            ],
        ];

        foreach ($unidades as $ubs) {
            DB::table('ubs')->updateOrInsert(
                ['nome' => $ubs['nome']],
                [
                    'endereco' => $ubs['endereco'],
                    'diretor' => $ubs['diretor'],
                    'contato' => $ubs['contato'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
