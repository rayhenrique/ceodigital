<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\Dentista;
use App\Models\DentistaGrade;
use App\Models\Especialidade;
use App\Models\Paciente;
use App\Models\Ubs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AgendaMensalTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Ubs $ubs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'operador',
            'status_ativo' => true,
        ]);

        $this->ubs = Ubs::create([
            'nome' => 'UBS Principal',
            'endereco' => 'Rua Central, 50',
            'diretor' => 'Dr. Coordenador',
            'contato' => '82999990000',
        ]);
    }

    /**
     * Testa acesso autenticado ao mapa mensal da agenda.
     */
    public function test_usuario_autenticado_pode_acessar_mapa_mensal_da_agenda(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('agenda.mensal'));

        $response->assertOk();
        $response->assertSee('Mapa Mensal da Agenda');
        $response->assertSee('Segunda');
        $response->assertSee('Terça');
        $response->assertSee('Sexta');
        $response->assertSee('Ocupação do Mês');
    }

    /**
     * Testa o cálculo de capacidade padrão e ocupação por dia no calendário mensal.
     */
    public function test_calculo_de_capacidade_e_ocupacao_mensal_por_dia(): void
    {
        $especialidade = Especialidade::create([
            'nome' => 'Endodontia',
            'descricao' => 'Canal',
            'tempo_medio_minutos' => 60,
        ]);

        $dentista = Dentista::create([
            'nome_completo' => 'Dr. Roberto Canal',
            'cro' => 'CRO-9988',
            'telefone' => '82988887777',
            'especialidade_id' => $especialidade->id,
            'status_ativo' => true,
        ]);

        // Grade operacional: Terças-feiras (dia_semana = 2), manhã, 8 vagas
        DentistaGrade::create([
            'dentista_id' => $dentista->id,
            'dia_semana' => 2,
            'turno' => 'manha',
            'vagas_padrao' => 8,
        ]);

        $paciente = Paciente::create([
            'nome_completo' => 'Paciente Teste Mensal',
            'cpf' => '52998224725',
            'data_nascimento' => '1990-01-01',
            'sexo' => 'M',
            'telefone_1' => '82999991111',
            'ubs_id' => $this->ubs->id,
        ]);

        // Encontrar a próxima terça-feira do mês atual
        $dataTerca = now()->startOfMonth()->next(Carbon::TUESDAY);

        Agendamento::create([
            'paciente_id' => $paciente->id,
            'dentista_id' => $dentista->id,
            'especialidade_id' => $especialidade->id,
            'user_id' => $this->user->id,
            'data_agendamento' => $dataTerca->toDateString(),
            'turno' => 'manha',
            'tipo' => 'normal',
            'status' => 'agendado',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('agenda.mensal', [
                'mes' => $dataTerca->month,
                'ano' => $dataTerca->year,
            ]));

        $response->assertOk();
        // Verifica que o dia tem 1 agendado de 8 vagas (1 / 8)
        $response->assertSee('1');
        $response->assertSee('/ 8');
    }

    /**
     * Testa filtros de especialidade e dentista no mapa mensal.
     */
    public function test_filtro_de_especialidade_e_dentista_no_mapa_mensal(): void
    {
        $esp1 = Especialidade::create(['nome' => 'Periodontia', 'descricao' => 'Gengiva', 'tempo_medio_minutos' => 45]);
        $esp2 = Especialidade::create(['nome' => 'Cirurgia Bucomaxilo', 'descricao' => 'Extração', 'tempo_medio_minutos' => 40]);

        $dentista1 = Dentista::create([
            'nome_completo' => 'Dr. Periodontista',
            'cro' => 'CRO-1111',
            'telefone' => '82911112222',
            'especialidade_id' => $esp1->id,
            'status_ativo' => true,
        ]);

        $dentista2 = Dentista::create([
            'nome_completo' => 'Dr. Cirurgião',
            'cro' => 'CRO-2222',
            'telefone' => '82922223333',
            'especialidade_id' => $esp2->id,
            'status_ativo' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('agenda.mensal', [
                'especialidade_id' => $esp1->id,
                'dentista_id' => $dentista1->id,
            ]));

        $response->assertOk();
        $response->assertSee('Periodontia');
        $response->assertSee('Dr. Periodontista');
    }

    /**
     * Testa navegação entre meses específicos.
     */
    public function test_navegacao_entre_meses_diferentes(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('agenda.mensal', ['mes' => 12, 'ano' => 2026]));

        $response->assertOk();
        $response->assertSee('Dezembro de 2026');
    }
}
