<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\DemandaReprimida;
use App\Models\Dentista;
use App\Models\Especialidade;
use App\Models\Paciente;
use App\Models\Ubs;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EspecialidadeCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'status_ativo' => true,
        ]);
    }

    /**
     * Testa a listagem de especialidades.
     */
    public function test_pode_listar_especialidades(): void
    {
        Especialidade::create([
            'nome' => 'Endodontia Avançada',
            'descricao' => 'Tratamentos de canal complexos',
            'status_ativo' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('especialidades.index'));

        $response->assertOk();
        $response->assertSee('Endodontia Avançada');
    }

    /**
     * Testa o cadastro de uma nova especialidade.
     */
    public function test_pode_cadastrar_nova_especialidade(): void
    {
        $dados = [
            'nome' => 'Ortodontia Preventiva',
            'descricao' => 'Aparelhos ortopédicos preventivos',
            'status_ativo' => '1',
        ];

        $response = $this->actingAs($this->admin)->post(route('especialidades.store'), $dados);

        $response->assertRedirect(route('especialidades.index'));
        $this->assertDatabaseHas('especialidades', ['nome' => 'Ortodontia Preventiva']);
    }

    /**
     * Testa a atualização de uma especialidade existente.
     */
    public function test_pode_atualizar_dados_da_especialidade(): void
    {
        $esp = Especialidade::create([
            'nome' => 'Periodontia Básica',
            'descricao' => 'Tratamento gengival',
            'status_ativo' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('especialidades.update', $esp), [
            'nome' => 'Periodontia Especializada',
            'descricao' => 'Cirurgias periodontais e enxertos',
            'status_ativo' => '1',
        ]);

        $response->assertRedirect(route('especialidades.index'));
        $this->assertDatabaseHas('especialidades', [
            'id' => $esp->id,
            'nome' => 'Periodontia Especializada',
        ]);
    }

    /**
     * Testa a exclusão de uma especialidade sem pacientes nem dentistas vinculados (permitida).
     */
    public function test_pode_excluir_especialidade_sem_pacientes_vinculados(): void
    {
        $esp = Especialidade::create([
            'nome' => 'Especialidade Provisória',
            'descricao' => 'Sem atendimentos ou fila',
            'status_ativo' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('especialidades.destroy', $esp));

        $response->assertRedirect(route('especialidades.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('especialidades', ['id' => $esp->id]);
    }

    /**
     * Testa bloqueio de exclusão quando a especialidade possui agendamentos de pacientes.
     */
    public function test_bloqueia_exclusao_de_especialidade_com_agendamentos_de_pacientes(): void
    {
        $esp = Especialidade::create([
            'nome' => 'Cirurgia Bucomaxilofacial',
            'status_ativo' => true,
        ]);

        $ubs = Ubs::create(['nome' => 'UBS Central', 'contato' => '8232110000']);
        $paciente = Paciente::create([
            'ubs_id' => $ubs->id,
            'nome_completo' => 'Paciente Agendado',
            'cpf' => '11122233344',
            'cns' => '700000000000002',
            'data_nascimento' => '1985-05-15',
            'sexo' => 'F',
            'telefone_1' => '82999991111',
        ]);

        $dentista = Dentista::create([
            'especialidade_id' => $esp->id,
            'nome_completo' => 'Dr. Cirurgião Teste',
            'cro' => 'CRO-AL 12345',
            'status_ativo' => true,
        ]);

        Agendamento::create([
            'paciente_id' => $paciente->id,
            'dentista_id' => $dentista->id,
            'especialidade_id' => $esp->id,
            'user_id' => $this->admin->id,
            'data_agendamento' => Carbon::tomorrow()->toDateString(),
            'turno' => 'manha',
            'tipo' => 'normal',
            'status' => 'agendado',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('especialidades.destroy', $esp));

        $response->assertRedirect(route('especialidades.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('especialidades', ['id' => $esp->id]);
    }

    /**
     * Testa bloqueio de exclusão quando a especialidade possui pacientes na fila de espera.
     */
    public function test_bloqueia_exclusao_de_especialidade_com_pacientes_na_fila_de_espera(): void
    {
        $esp = Especialidade::create([
            'nome' => 'Odontopediatria Especial',
            'status_ativo' => true,
        ]);

        $ubs = Ubs::create(['nome' => 'UBS Sul', 'contato' => '8232110001']);
        $paciente = Paciente::create([
            'ubs_id' => $ubs->id,
            'nome_completo' => 'Criança Fila Espera',
            'cpf' => '22233344455',
            'cns' => '700000000000003',
            'data_nascimento' => '2018-10-10',
            'sexo' => 'M',
            'telefone_1' => '82999992222',
        ]);

        DemandaReprimida::create([
            'paciente_id' => $paciente->id,
            'especialidade_id' => $esp->id,
            'prioridade' => 'normal',
            'status' => 'aguardando',
            'data_solicitacao' => Carbon::now()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('especialidades.destroy', $esp));

        $response->assertRedirect(route('especialidades.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('especialidades', ['id' => $esp->id]);
    }
}
