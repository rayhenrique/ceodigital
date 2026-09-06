<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Agendamento;
use App\Models\Auditoria;
use App\Models\DemandaReprimida;
use App\Models\Dentista;
use App\Models\DentistaGrade;
use App\Models\Especialidade;
use App\Models\Paciente;
use App\Models\Ubs;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    /**
     * Gera um CPF matematicamente válido com os dois dígitos verificadores corretos.
     */
    private function gerarCpfValido(): string
    {
        $n = [];
        for ($i = 0; $i < 9; $i++) {
            $n[] = rand(0, 9);
        }

        // 1º dígito
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += $n[$i] * (10 - $i);
        }
        $resto = ($soma * 10) % 11;
        $d1 = ($resto === 10 || $resto === 11) ? 0 : $resto;
        $n[] = $d1;

        // 2º dígito
        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += $n[$i] * (11 - $i);
        }
        $resto = ($soma * 10) % 11;
        $d2 = ($resto === 10 || $resto === 11) ? 0 : $resto;
        $n[] = $d2;

        return implode('', $n);
    }

    /**
     * Executa a população com dados fictícios ricos para testes de uso.
     */
    public function run(): void
    {
        $this->command?->info('Iniciando carga de dados fictícios para testes locais...');

        // 1. Executa seeders base
        $this->call([
            AdminUserSeeder::class,
            EspecialidadeSeeder::class,
            UbsSeeder::class,
        ]);

        $adminUser = User::where('role', 'admin')->first();
        $adminId = $adminUser ? $adminUser->id : 1;

        // 2. Cria operadores para teste de perfis
        $operadores = [
            ['name' => 'Ana Recepção', 'email' => 'operador@ceodigital.gov.br', 'role' => 'operador'],
            ['name' => 'Carlos Regulação', 'email' => 'regulacao@ceodigital.gov.br', 'role' => 'operador'],
            ['name' => 'Juliana Triagem', 'email' => 'triagem@ceodigital.gov.br', 'role' => 'operador'],
        ];

        foreach ($operadores as $op) {
            User::updateOrCreate(
                ['email' => $op['email']],
                [
                    'name' => $op['name'],
                    'password' => Hash::make('operador123'),
                    'role' => $op['role'],
                    'status_ativo' => true,
                    'email_verified_at' => now(),
                ]
            );
        }

        // 3. Cadastra Dentistas Especialistas e suas Grades Operacionais
        $especialidades = Especialidade::all()->keyBy('nome');
        $ubsList = Ubs::all();

        $dadosDentistas = [
            [
                'nome' => 'Dr. Marcelo Cavalcante',
                'cro' => 'CRO-AL 4521',
                'telefone' => '(82) 98877-1101',
                'esp' => 'Endodontia',
                'grades' => [
                    ['dia' => 1, 'turno' => 'manha', 'vagas' => 8], // Segunda manhã
                    ['dia' => 3, 'turno' => 'tarde', 'vagas' => 8], // Quarta tarde
                    ['dia' => 5, 'turno' => 'manha', 'vagas' => 6], // Sexta manhã
                ],
            ],
            [
                'nome' => 'Dra. Camila Albuquerque',
                'cro' => 'CRO-AL 5892',
                'telefone' => '(82) 98877-1102',
                'esp' => 'Periodontia Especializada',
                'grades' => [
                    ['dia' => 2, 'turno' => 'manha', 'vagas' => 8], // Terça manhã
                    ['dia' => 4, 'turno' => 'tarde', 'vagas' => 8], // Quinta tarde
                ],
            ],
            [
                'nome' => 'Dr. Rafael Farias',
                'cro' => 'CRO-AL 3310',
                'telefone' => '(82) 98877-1103',
                'esp' => 'Cirurgia Oral Menor',
                'grades' => [
                    ['dia' => 1, 'turno' => 'tarde', 'vagas' => 10], // Segunda tarde
                    ['dia' => 3, 'turno' => 'manha', 'vagas' => 10], // Quarta manhã
                    ['dia' => 5, 'turno' => 'tarde', 'vagas' => 8],  // Sexta tarde
                ],
            ],
            [
                'nome' => 'Dra. Larissa Menezes',
                'cro' => 'CRO-AL 6744',
                'telefone' => '(82) 98877-1104',
                'esp' => 'Odontopediatria',
                'grades' => [
                    ['dia' => 2, 'turno' => 'tarde', 'vagas' => 8], // Terça tarde
                    ['dia' => 4, 'turno' => 'manha', 'vagas' => 8], // Quinta manhã
                ],
            ],
            [
                'nome' => 'Dr. Bruno Silveira',
                'cro' => 'CRO-AL 4980',
                'telefone' => '(82) 98877-1105',
                'esp' => 'Atendimento a Pacientes com Necessidades Especiais (PNE)',
                'grades' => [
                    ['dia' => 3, 'turno' => 'manha', 'vagas' => 6], // Quarta manhã
                    ['dia' => 5, 'turno' => 'manha', 'vagas' => 6], // Sexta manhã
                ],
            ],
            [
                'nome' => 'Dra. Mariana Brandão',
                'cro' => 'CRO-AL 7120',
                'telefone' => '(82) 98877-1106',
                'esp' => 'Ortodontia / Ortopedia Facial',
                'grades' => [
                    ['dia' => 1, 'turno' => 'manha', 'vagas' => 12], // Segunda manhã
                    ['dia' => 4, 'turno' => 'manha', 'vagas' => 12], // Quinta manhã
                ],
            ],
            [
                'nome' => 'Dr. Thiago Vianna',
                'cro' => 'CRO-AL 3801',
                'telefone' => '(82) 98877-1107',
                'esp' => 'Prótese Dentária',
                'grades' => [
                    ['dia' => 2, 'turno' => 'manha', 'vagas' => 8], // Terça manhã
                    ['dia' => 5, 'turno' => 'tarde', 'vagas' => 8], // Sexta tarde
                ],
            ],
            [
                'nome' => 'Dra. Beatriz Sampaio',
                'cro' => 'CRO-AL 5402',
                'telefone' => '(82) 98877-1108',
                'esp' => 'Diagnóstico Bucal / Estomatologia',
                'grades' => [
                    ['dia' => 1, 'turno' => 'tarde', 'vagas' => 8], // Segunda tarde
                    ['dia' => 4, 'turno' => 'tarde', 'vagas' => 8], // Quinta tarde
                ],
            ],
        ];

        $dentistasCadastrados = [];
        foreach ($dadosDentistas as $d) {
            $espModel = $especialidades->get($d['esp']);
            if (! $espModel) {
                continue;
            }

            $dentista = Dentista::updateOrCreate(
                ['cro' => $d['cro']],
                [
                    'nome_completo' => $d['nome'],
                    'telefone' => $d['telefone'],
                    'especialidade_id' => $espModel->id,
                    'status_ativo' => true,
                ]
            );

            $dentistasCadastrados[] = $dentista;

            // Cadastra ou atualiza grades
            foreach ($d['grades'] as $g) {
                DentistaGrade::updateOrCreate(
                    [
                        'dentista_id' => $dentista->id,
                        'dia_semana' => $g['dia'],
                        'turno' => $g['turno'],
                    ],
                    [
                        'vagas_padrao' => $g['vagas'],
                    ]
                );
            }
        }

        // 4. Cadastra 60 Pacientes Fictícios Realistas
        $nomesFicticios = [
            'Maria José da Silva', 'José Carlos dos Santos', 'Ana Paula de Oliveira', 'João Batista Ferreira',
            'Francisca Pereira Lima', 'Antônio Marcos da Rocha', 'Juliana Costa e Silva', 'Lucas Gabriel de Souza',
            'Márcia Regina Martins', 'Carlos Eduardo Ribeiro', 'Patrícia Helena Duarte', 'Francisco de Assis Alves',
            'Sônia Maria de Vasconcelos', 'Rodrigo Mendes Albuquerque', 'Camila Vitória dos Santos', 'Pedro Henrique Peixoto',
            'Fernanda Aparecida Gomes', 'Cláudio Roberto Teixeira', 'Luciana Maria da Conceição', 'Gabriel Vinícius Moreira',
            'Aline Beatriz Figueiredo', 'Severino Manoel de Lima', 'Tereza Cristina Barbosa', 'Marcos Vinícius Cavalcante',
            'Renata Kelly Farias', 'Danilo César Guimarães', 'Larissa Gabrielle Moura', 'Alexandre Magno Brandão',
            'Vanessa Cristina Nogueira', 'Tiago André de Holanda', 'Simone Cristina Batista', 'Fábio Júnior de Souza',
            'Rita de Cássia Medeiros', 'Gustavo Henrique Toledo', 'Bruna Rafaela Calheiros', 'Diego Rafael de Arruda',
            'Carla Graziele Pimentel', 'Leandro Augusto de Miranda', 'Priscila Dayane Silveira', 'Vitor Hugo Magalhães',
            'Amanda Caroline de Barros', 'Raimundo Nonato Dantas', 'Rosângela Maria de Melo', 'Edmilson Carlos Barreto',
            'Kátia Cilene Tavares', 'Wellington José de Aguiar', 'Monique Stephany Viana', 'Geraldo Magela Portela',
            'Talita Fernanda de Castro', 'Cícero Romão de Albuquerque', 'Marlene Gonçalves da Paz', 'Edivaldo Silva de Lucena',
            'Adriana Carla de Macedo', 'Samuel David da Costa', 'Natália Cristina de Morais', 'Igor Matheus Fontes',
            'Débora Luísa Campelo', 'Valter Hugo de Alcântara', 'Eliane Soares de Mendonça', 'Jonas Felipe Rezende',
        ];

        $bairros = ['Centro', 'Ponta Verde', 'Jatiúca', 'Farol', 'Benedito Bentes', 'Trapiche', 'Serraria', 'Jacintinho', 'Feitosa', 'Poço'];
        $acss = ['ACS Dona Maria', 'ACS Seu Jorge', 'ACS Sandra', 'ACS Marcos', 'ACS Valéria', 'ACS Robson'];

        $pacientes = [];
        $cpfsUsados = [];

        foreach ($nomesFicticios as $index => $nome) {
            do {
                $cpf = $this->gerarCpfValido();
            } while (in_array($cpf, $cpfsUsados, true));
            $cpfsUsados[] = $cpf;

            $nascimento = now()->subYears(rand(6, 75))->subDays(rand(1, 350));
            $sexo = rand(0, 1) === 1 ? 'F' : 'M';
            $ubs = $ubsList->random();
            $bairro = $bairros[array_rand($bairros)];
            $cns = '7' . str_pad((string) rand(10000000000000, 99999999999999), 14, '0', STR_PAD_LEFT);

            $paciente = Paciente::updateOrCreate(
                ['nome_completo' => $nome],
                [
                    'cpf' => $cpf,
                    'cns' => $cns,
                    'data_nascimento' => $nascimento->format('Y-m-d'),
                    'sexo' => $sexo,
                    'ubs_id' => $ubs->id,
                    'telefone_1' => '(82) 9' . rand(8000, 9999) . '-' . rand(1000, 9999),
                    'telefone_2' => rand(0, 2) === 1 ? '(82) 3' . rand(200, 399) . '-' . rand(1000, 9999) : null,
                    'nome_acs' => $acss[array_rand($acss)],
                    'endereco' => 'Rua das Flores, ' . rand(10, 999) . ', ' . $bairro,
                ]
            );

            $pacientes[] = $paciente;
        }

        // 5. Gera Agendamentos com Variação de Status e Lotações
        $hoje = now();
        $dentistasCollection = collect($dentistasCadastrados);

        // Agendamentos de HOJE (para ver a recepção e fluxo operacional acontecendo)
        $dentistasHoje = $dentistasCollection->take(4);
        $pacienteIndex = 0;

        foreach ($dentistasHoje as $dentista) {
            $espId = $dentista->especialidade_id;

            // Turno da Manhã de Hoje
            $horariosManha = ['07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30'];
            $statusesManha = ['concluido', 'concluido', 'em_atendimento', 'presente', 'presente', 'agendado', 'falta'];

            foreach ($horariosManha as $i => $hora) {
                if (! isset($pacientes[$pacienteIndex])) {
                    $pacienteIndex = 0;
                }
                $p = $pacientes[$pacienteIndex++];

                Agendamento::create([
                    'paciente_id' => $p->id,
                    'dentista_id' => $dentista->id,
                    'especialidade_id' => $espId,
                    'user_id' => $adminId,
                    'data_agendamento' => $hoje->toDateString(),
                    'turno' => 'manha',
                    'tipo' => 'normal',
                    'status' => $statusesManha[$i] ?? 'agendado',
                    'horario_chegada' => in_array($statusesManha[$i] ?? '', ['presente', 'em_atendimento', 'concluido'], true) ? $hora . ':00' : null,
                    'observacao' => 'Paciente de retorno para continuidade do tratamento odontológico.',
                ]);
            }

            // 1 Encaixe de Emergência na manhã de hoje
            if (isset($pacientes[$pacienteIndex])) {
                Agendamento::create([
                    'paciente_id' => $pacientes[$pacienteIndex++]->id,
                    'dentista_id' => $dentista->id,
                    'especialidade_id' => $espId,
                    'user_id' => $adminId,
                    'data_agendamento' => $hoje->toDateString(),
                    'turno' => 'manha',
                    'tipo' => 'encaixe',
                    'status' => 'presente',
                    'horario_chegada' => '09:15:00',
                    'observacao' => 'Encaixe de urgência: dor aguda e edema facial.',
                ]);
            }
        }

        // Agendamentos em outros dias do mês (Passados e Futuros)
        // Percorre os dias do mês atual para criar variedade no mapa mensal
        $inicioMes = $hoje->copy()->startOfMonth();
        $fimMes = $hoje->copy()->endOfMonth();
        $diaAtual = $inicioMes->copy();

        while ($diaAtual->lte($fimMes)) {
            // Pula fins de semana para dias normais
            if ($diaAtual->isWeekend() || $diaAtual->isSameDay($hoje)) {
                $diaAtual->addDay();
                continue;
            }

            $diaSemana = $diaAtual->dayOfWeekIso;
            $gradesDoDia = DentistaGrade::where('dia_semana', $diaSemana)->get();

            if ($gradesDoDia->isEmpty()) {
                $diaAtual->addDay();
                continue;
            }

            // Define um perfil para o dia (Lotado, Moderado, Livre)
            $perfilDia = match ($diaAtual->day % 4) {
                0 => 'lotado',     // 100% preenchido
                1 => 'quase_cheio',// 80% preenchido
                2 => 'moderado',   // 50% preenchido
                default => 'livre',// 25% preenchido
            };

            foreach ($gradesDoDia as $grade) {
                $vagas = $grade->vagas_padrao;
                $qtdAgendar = match ($perfilDia) {
                    'lotado' => $vagas + 1, // Encaixe
                    'quase_cheio' => (int) ceil($vagas * 0.85),
                    'moderado' => (int) ceil($vagas * 0.55),
                    default => (int) ceil($vagas * 0.25),
                };

                for ($k = 0; $k < $qtdAgendar; $k++) {
                    if (! isset($pacientes[$pacienteIndex])) {
                        $pacienteIndex = 0;
                    }
                    $p = $pacientes[$pacienteIndex++];

                    $isEncaixe = $k >= $vagas;
                    $status = 'agendado';

                    if ($diaAtual->isPast()) {
                        $status = rand(0, 5) === 0 ? 'falta' : 'concluido';
                    }

                    Agendamento::create([
                        'paciente_id' => $p->id,
                        'dentista_id' => $grade->dentista_id,
                        'especialidade_id' => $grade->dentista->especialidade_id,
                        'user_id' => $adminId,
                        'data_agendamento' => $diaAtual->toDateString(),
                        'turno' => $grade->turno,
                        'tipo' => $isEncaixe ? 'encaixe' : 'normal',
                        'status' => $status,
                        'horario_chegada' => $status === 'concluido' ? '08:15:00' : null,
                        'observacao' => $isEncaixe ? 'Encaixe operacional' : 'Agendamento regulado via UBS',
                    ]);
                }
            }

            $diaAtual->addDay();
        }

        // 6. Cadastra 20 Pacientes na Fila de Espera (Demanda Reprimida)
        $turnosPreferenciais = ['manha', 'tarde', 'qualquer'];
        $prioridades = ['normal', 'normal', 'normal', 'urgente'];

        for ($f = 0; $f < 20; $f++) {
            if (! isset($pacientes[$pacienteIndex])) {
                $pacienteIndex = 0;
            }
            $p = $pacientes[$pacienteIndex++];
            $esp = $especialidades->random();

            DemandaReprimida::create([
                'paciente_id' => $p->id,
                'especialidade_id' => $esp->id,
                'turno_preferencial' => $turnosPreferenciais[array_rand($turnosPreferenciais)],
                'prioridade' => $prioridades[array_rand($prioridades)],
                'status' => 'aguardando',
                'data_solicitacao' => now()->subDays(rand(2, 45))->toDateString(),
                'observacoes' => 'Encaminhado pela UBS de referência com guia de encaminhamento odontológico.',
            ]);
        }

        // 7. Gera registros de Auditoria para simular histórico real
        Auditoria::create([
            'user_id' => $adminId,
            'acao' => 'CRIAR',
            'tabela_afetada' => 'dentistas',
            'registro_id' => 1,
            'dados_anteriores' => null,
            'dados_novos' => ['nome_completo' => 'Dr. Marcelo Cavalcante', 'cro' => 'CRO-AL 4521'],
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(15),
        ]);

        Auditoria::create([
            'user_id' => $adminId,
            'acao' => 'ATUALIZAR',
            'tabela_afetada' => 'dentista_grades',
            'registro_id' => 1,
            'dados_anteriores' => ['vagas_padrao' => 6],
            'dados_novos' => ['vagas_padrao' => 8],
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(10),
        ]);

        $this->command?->info('Carga de dados fictícios concluída com sucesso!');
        $this->command?->info('- 60 Pacientes cadastrados');
        $this->command?->info('- 8 Dentistas com escalas operacionais');
        $this->command?->info('- Dezenas de agendamentos no mês (Livre, Moderado, Quase Cheio, Lotado com Encaixes)');
        $this->command?->info('- 20 Pacientes na Fila de Espera (Demanda Reprimida)');
        $this->command?->info('- Usuários de teste: admin@ceodigital.gov.br (admin123), operador@ceodigital.gov.br (operador123)');
    }
}
