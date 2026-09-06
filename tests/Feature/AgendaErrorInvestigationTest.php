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
use Tests\TestCase;

class AgendaErrorInvestigationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Ubs $ubs;
    protected Especialidade $especialidade;
    protected Dentista $dentista;
    protected Paciente $paciente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'admin',
            'status_ativo' => true,
        ]);

        $this->ubs = Ubs::create(['nome' => 'UBS Central']);

        $this->especialidade = Especialidade::create([
            'nome' => 'Endodontia',
            'status_ativo' => true,
        ]);

        $this->dentista = Dentista::create([
            'especialidade_id' => $this->especialidade->id,
            'nome_completo' => 'Dr. Especialista',
            'cro' => 'CRO 1234',
            'status_ativo' => true,
        ]);

        $this->paciente = Paciente::create([
            'ubs_id' => $this->ubs->id,
            'cpf' => '12345678901',
            'nome_completo' => 'Paciente Teste',
            'data_nascimento' => '1990-01-01',
            'sexo' => 'M',
            'telefone_1' => '82999998888',
        ]);
    }

    public function test_get_agenda_sem_grade_para_o_dia(): void
    {
        // 2026-09-08 é terça-feira. O dentista NÃO tem grade cadastrada na terça.
        $response = $this->actingAs($this->user)
            ->get('/agenda?data=2026-09-08&turno=manha');

        $response->assertStatus(200);
    }

    public function test_post_agenda_sem_grade_deve_retornar_validacao(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/agenda', [
                'paciente_id' => $this->paciente->id,
                'dentista_id' => $this->dentista->id,
                'especialidade_id' => $this->especialidade->id,
                'data_agendamento' => '2026-09-08',
                'turno' => 'manha',
                'tipo' => 'normal',
            ]);

        $response->assertSessionHasErrors('turno');
    }

    public function test_post_agenda_com_dados_do_form_create_sem_grade_retorna_alerta_amigavel(): void
    {
        // No form agenda/create, especialidade_id não é enviado (é auto-preenchido do dentista)
        $response = $this->actingAs($this->user)
            ->post('/agenda', [
                'paciente_id' => $this->paciente->id,
                'dentista_id' => $this->dentista->id,
                'data_agendamento' => '2026-09-08', // Terça-feira (sem grade)
                'turno' => 'manha',
                'tipo' => 'normal',
            ]);

        // Validação da grade falha com mensagem amigável no campo turno
        $response->assertSessionHasErrors('turno');
        $this->assertStringContainsString(
            'não possui escala de atendimento cadastrada para Terça-feira no turno da Manhã',
            session('errors')->first('turno')
        );
    }

    public function test_promover_demanda_reprimida_sem_grade_retorna_alerta(): void
    {
        $demanda = \App\Models\DemandaReprimida::create([
            'paciente_id' => $this->paciente->id,
            'especialidade_id' => $this->especialidade->id,
            'data_solicitacao' => '2026-09-01',
            'turno_preferencial' => 'manha',
            'prioridade' => 'normal',
            'status' => 'aguardando',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('demanda-reprimida.promover', $demanda), [
                'dentista_id' => $this->dentista->id,
                'data_agendamento' => '2026-09-08', // Terça-feira sem grade
                'turno' => 'manha',
                'tipo' => 'normal',
            ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString(
            'não possui escala cadastrada para Terça-feira no turno da Manhã',
            session('error')
        );
    }

    public function test_agenda_index_renderiza_corretamente_com_agendamento_e_rota_chegada(): void
    {
        // Cria grade para o dentista na terça-feira
        DentistaGrade::create([
            'dentista_id' => $this->dentista->id,
            'dia_semana' => 2, // Terça-feira
            'turno' => 'manha',
            'vagas_padrao' => 8,
        ]);

        // Cria o agendamento
        $agendamento = Agendamento::create([
            'paciente_id' => $this->paciente->id,
            'dentista_id' => $this->dentista->id,
            'especialidade_id' => $this->especialidade->id,
            'user_id' => $this->user->id,
            'data_agendamento' => '2026-09-08',
            'turno' => 'manha',
            'tipo' => 'normal',
            'status' => 'agendado',
        ]);

        // Acessa a agenda do dia com agendamento
        $responseAgenda = $this->actingAs($this->user)
            ->get(route('agenda.index', [
                'data' => '2026-09-08',
                'turno' => 'manha',
            ]));

        $responseAgenda->assertStatus(200);
        $responseAgenda->assertSee('Registrar Chegada');
        $responseAgenda->assertSee($this->paciente->nome_completo);

        // Testa o clique no botão "Registrar Chegada" (route agenda.chegada)
        $responseChegada = $this->actingAs($this->user)
            ->post(route('agenda.chegada', $agendamento));

        $responseChegada->assertRedirect();
        $responseChegada->assertSessionHas('success');

        $this->assertEquals('presente', $agendamento->fresh()->status);
        $this->assertNotNull($agendamento->fresh()->horario_chegada);
    }
}
