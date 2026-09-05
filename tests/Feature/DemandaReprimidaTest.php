<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\DemandaReprimida;
use App\Models\Dentista;
use App\Models\DentistaGrade;
use App\Models\Especialidade;
use App\Models\Paciente;
use App\Models\Ubs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DemandaReprimidaTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Ubs $ubs;
    protected Especialidade $especialidade;
    protected Dentista $dentista;
    protected string $dataConsulta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'admin',
            'status_ativo' => true,
        ]);

        $this->ubs = Ubs::create([
            'nome' => 'UBS Pajuçara',
        ]);

        $this->especialidade = Especialidade::create([
            'nome' => 'Cirurgia Bucomaxilofacial',
            'status_ativo' => true,
        ]);

        $this->dentista = Dentista::create([
            'especialidade_id' => $this->especialidade->id,
            'nome_completo' => 'Dr. Marcelo Cirurgião',
            'cro' => 'CRO-AL 5555',
            'status_ativo' => true,
        ]);

        // Próxima terça-feira (dia 2)
        $proximaTerca = Carbon::now()->next(Carbon::TUESDAY);
        $this->dataConsulta = $proximaTerca->toDateString();

        DentistaGrade::create([
            'dentista_id' => $this->dentista->id,
            'dia_semana' => 2,
            'turno' => 'tarde',
            'vagas_padrao' => 10,
        ]);
    }

    /**
     * Testa inserção na fila de espera e priorização de urgência sobre casos normais (RF19).
     */
    public function test_insercao_e_ordenacao_por_prioridade_na_fila_de_espera(): void
    {
        $pacienteNormal = Paciente::create([
            'ubs_id' => $this->ubs->id,
            'cpf' => '52998224725',
            'nome_completo' => 'Paciente da Fila Normal',
            'data_nascimento' => '1990-01-01',
            'sexo' => 'M',
            'telefone_1' => '82999991111',
        ]);

        $pacienteUrgente = Paciente::create([
            'ubs_id' => $this->ubs->id,
            'cpf' => '65432198700',
            'nome_completo' => 'Paciente da Fila Urgente',
            'data_nascimento' => '1985-06-06',
            'sexo' => 'F',
            'telefone_1' => '82999992222',
        ]);

        // Cria primeiro o paciente normal
        $demandaNormal = DemandaReprimida::create([
            'paciente_id' => $pacienteNormal->id,
            'especialidade_id' => $this->especialidade->id,
            'prioridade' => 'normal',
            'status' => 'aguardando',
            'data_solicitacao' => now()->subDays(5)->toDateString(),
        ]);

        // Cria depois o paciente urgente
        $demandaUrgente = DemandaReprimida::create([
            'paciente_id' => $pacienteUrgente->id,
            'especialidade_id' => $this->especialidade->id,
            'prioridade' => 'urgente',
            'status' => 'aguardando',
            'data_solicitacao' => now()->toDateString(),
        ]);

        // Consulta usando o escopo de ordenação de fila (urgente tem precedência)
        $primeiroDaFila = DemandaReprimida::prioridadeFila()->first();

        $this->assertEquals($demandaUrgente->id, $primeiroDaFila->id);
    }

    /**
     * Testa promoção atômica da demanda reprimida para agendamento efetivo (RF20).
     */
    public function test_promocao_de_demanda_reprimida_para_agendamento(): void
    {
        $paciente = Paciente::create([
            'ubs_id' => $this->ubs->id,
            'cpf' => '52998224725',
            'nome_completo' => 'Paciente Para Promocao',
            'data_nascimento' => '1992-04-04',
            'sexo' => 'M',
            'telefone_1' => '82988884444',
        ]);

        $demanda = DemandaReprimida::create([
            'paciente_id' => $paciente->id,
            'especialidade_id' => $this->especialidade->id,
            'prioridade' => 'urgente',
            'status' => 'aguardando',
            'data_solicitacao' => now()->subDays(2)->toDateString(),
        ]);

        $dadosPromocao = [
            'dentista_id' => $this->dentista->id,
            'data_agendamento' => $this->dataConsulta,
            'turno' => 'tarde',
            'tipo' => 'normal',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('demanda-reprimida.promover', $demanda), $dadosPromocao);

        $response->assertRedirect();

        // Demanda reprimida deve ter status atualizado para 'agendado'
        $demanda->refresh();
        $this->assertEquals('agendado', $demanda->status);

        // Deve existir o novo agendamento criado no banco
        $this->assertDatabaseHas('agendamentos', [
            'paciente_id' => $paciente->id,
            'dentista_id' => $this->dentista->id,
            'especialidade_id' => $this->especialidade->id,
            'turno' => 'tarde',
            'status' => 'agendado',
        ]);

        $agendamentoCriado = Agendamento::where('paciente_id', $paciente->id)->first();
        $this->assertNotNull($agendamentoCriado);
        $this->assertEquals($this->dataConsulta, $agendamentoCriado->data_agendamento->format('Y-m-d'));
    }
}
