<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\Auditoria;
use App\Models\Dentista;
use App\Models\DentistaGrade;
use App\Models\Especialidade;
use App\Models\Paciente;
use App\Models\Ubs;
use App\Models\User;
use App\Services\AgendamentoService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AgendamentoEncaixeAuditoriaTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Ubs $ubs;
    protected Especialidade $especialidade;
    protected Dentista $dentista;
    protected string $dataTeste;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'admin',
            'status_ativo' => true,
        ]);

        $this->ubs = Ubs::create([
            'nome' => 'UBS Central',
        ]);

        $this->especialidade = Especialidade::create([
            'nome' => 'Endodontia',
            'status_ativo' => true,
        ]);

        $this->dentista = Dentista::create([
            'especialidade_id' => $this->especialidade->id,
            'nome_completo' => 'Dr. Fernando Especialista',
            'cro' => 'CRO-AL 9999',
            'status_ativo' => true,
        ]);

        // Próxima segunda-feira
        $proximaSegunda = Carbon::now()->next(Carbon::MONDAY);
        $this->dataTeste = $proximaSegunda->toDateString();

        // Grade para Segunda-feira (dia 1), Turno da Manhã, 8 vagas normais
        DentistaGrade::create([
            'dentista_id' => $this->dentista->id,
            'dia_semana' => 1,
            'turno' => 'manha',
            'vagas_padrao' => 8,
        ]);
    }

    /**
     * Testa o limite máximo de 2 encaixes por dentista no mesmo turno (RF14).
     */
    public function test_limite_maximo_de_dois_encaixes_por_turno(): void
    {
        $service = app(AgendamentoService::class);

        $paciente1 = Paciente::create([
            'ubs_id' => $this->ubs->id,
            'cpf' => '52998224725',
            'nome_completo' => 'Paciente Encaixe Um',
            'data_nascimento' => '1995-01-01',
            'sexo' => 'M',
            'telefone_1' => '82999991111',
        ]);

        $paciente2 = Paciente::create([
            'ubs_id' => $this->ubs->id,
            'cpf' => '65432198700',
            'nome_completo' => 'Paciente Encaixe Dois',
            'data_nascimento' => '1996-02-02',
            'sexo' => 'F',
            'telefone_1' => '82999992222',
        ]);

        $paciente3 = Paciente::create([
            'ubs_id' => $this->ubs->id,
            'cpf' => '12345678909',
            'nome_completo' => 'Paciente Encaixe Tres',
            'data_nascimento' => '1997-03-03',
            'sexo' => 'M',
            'telefone_1' => '82999993333',
        ]);

        // 1º Encaixe - Deve suceder
        $encaixe1 = $service->realizarEncaixe([
            'paciente_id' => $paciente1->id,
            'dentista_id' => $this->dentista->id,
            'data_agendamento' => $this->dataTeste,
            'turno' => 'manha',
            'observacoes' => 'Dor aguda no dente 16',
        ], $this->user->id);

        $this->assertEquals('encaixe', $encaixe1->tipo);

        // 2º Encaixe - Deve suceder
        $encaixe2 = $service->realizarEncaixe([
            'paciente_id' => $paciente2->id,
            'dentista_id' => $this->dentista->id,
            'data_agendamento' => $this->dataTeste,
            'turno' => 'manha',
            'observacoes' => 'Trauma dental',
        ], $this->user->id);

        $this->assertEquals('encaixe', $encaixe2->tipo);

        // 3º Encaixe - DEVE ser rejeitado por violar o limite máximo de 2 encaixes
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Limite de encaixes excedido');

        $service->realizarEncaixe([
            'paciente_id' => $paciente3->id,
            'dentista_id' => $this->dentista->id,
            'data_agendamento' => $this->dataTeste,
            'turno' => 'manha',
            'observacoes' => 'Tentativa de 3º encaixe',
        ], $this->user->id);
    }

    /**
     * Testa se mutações de dados geram registros automáticos na trilha de Auditoria (RF24).
     */
    public function test_mutacao_de_dados_registra_log_de_auditoria(): void
    {
        $this->actingAs($this->user);

        $paciente = Paciente::create([
            'ubs_id' => $this->ubs->id,
            'cpf' => '52998224725',
            'nome_completo' => 'Paciente Para Auditoria',
            'data_nascimento' => '1990-01-01',
            'sexo' => 'M',
            'telefone_1' => '82999990000',
        ]);

        // Verifica registro de auditoria na criação
        $this->assertDatabaseHas('auditorias', [
            'tabela_afetada' => 'pacientes',
            'registro_id' => $paciente->id,
            'acao' => 'pacientes.criado',
        ]);

        // Atualização do paciente
        $paciente->update(['nome_completo' => 'Nome Atualizado do Paciente']);

        // Verifica registro de auditoria na atualização
        $this->assertDatabaseHas('auditorias', [
            'tabela_afetada' => 'pacientes',
            'registro_id' => $paciente->id,
            'acao' => 'pacientes.atualizado',
        ]);
    }
}
