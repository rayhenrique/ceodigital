<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EspecialidadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $especialidades = [
            [
                'nome' => 'Diagnóstico Bucal / Estomatologia',
                'descricao' => 'Diagnóstico clínico com ênfase na detecção precoce do câncer de boca e lesões do complexo maxilofacial.',
                'status_ativo' => true,
            ],
            [
                'nome' => 'Periodontia Especializada',
                'descricao' => 'Tratamento de doenças periodontais moderadas e severas, cirurgias periodontais e cirurgias de enxerto.',
                'status_ativo' => true,
            ],
            [
                'nome' => 'Cirurgia Oral Menor',
                'descricao' => 'Cirurgia oral menor dos tecidos moles e duros, remoção de dentes inclusos/impactados e realização de biópsias.',
                'status_ativo' => true,
            ],
            [
                'nome' => 'Endodontia',
                'descricao' => 'Tratamento e retratamento endodôntico de dentes anteriores e posteriores (tratamento de canal).',
                'status_ativo' => true,
            ],
            [
                'nome' => 'Atendimento a Pacientes com Necessidades Especiais (PNE)',
                'descricao' => 'Atenção odontológica especializada a pacientes com deficiências físicas, intelectuais, síndromes e doenças crônicas graves.',
                'status_ativo' => true,
            ],
            [
                'nome' => 'Odontopediatria',
                'descricao' => 'Atenção odontológica especializada a bebês, crianças e adolescentes de alta complexidade comportamental ou clínica.',
                'status_ativo' => true,
            ],
            [
                'nome' => 'Ortodontia / Ortopedia Facial',
                'descricao' => 'Prevenção e tratamento interceptativo de más oclusões dentofaciais.',
                'status_ativo' => true,
            ],
            [
                'nome' => 'Prótese Dentária',
                'descricao' => 'Reabilitação protética por meio de próteses totais e removíveis para restabelecimento funcional e estético.',
                'status_ativo' => true,
            ],
        ];

        foreach ($especialidades as $esp) {
            DB::table('especialidades')->updateOrInsert(
                ['nome' => $esp['nome']],
                [
                    'descricao' => $esp['descricao'],
                    'status_ativo' => $esp['status_ativo'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
