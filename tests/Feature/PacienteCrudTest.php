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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PacienteCrudTest extends TestCase
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
            'nome' => 'UBS Central',
            'endereco' => 'Rua das Flores, 100',
            'diretor' => 'Dra. Coordenadora',
            'contato' => '82999991111',
        ]);
    }

    /**
     * Testa listagem de pacientes e filtros por busca e UBS.
     */
    public function test_usuario_autenticado_pode_listar_pacientes_com_filtro_de_ubs_e_busca(): void
    {
        $ubs2 = Ubs::create([
            'nome' => 'UBS Bairro Novo',
            'endereco' => 'Av Nova, 200',
            'diretor' => 'Dr. Novo',
            'contato' => '82999992222',
        ]);

        $paciente1 = Paciente::create([
            'nome_completo' => 'Severino da Silva',
            'cpf' => '52998224725',
            'data_nascimento' => '1980-01-01',
            'sexo' => 'M',
            'telefone_1' => '82988880001',
            'ubs_id' => $this->ubs->id,
        ]);

        $paciente2 = Paciente::create([
            'nome_completo' => 'Francisca Pereira',
            'cpf' => '12345678909', // CPF fictício
            'data_nascimento' => '1985-05-05',
            'sexo' => 'F',
            'telefone_1' => '82988880002',
            'ubs_id' => $ubs2->id,
        ]);

        // Busca por nome
        $resBusca = $this->actingAs($this->user)
            ->get(route('pacientes.index', ['busca' => 'Severino']))
            ->assertOk()
            ->assertSee('Severino da Silva')
            ->assertDontSee('Francisca Pereira');

        // Filtro por UBS
        $resUbs = $this->actingAs($this->user)
            ->get(route('pacientes.index', ['ubs_id' => $ubs2->id]))
            ->assertOk()
            ->assertSee('Francisca Pereira')
            ->assertDontSee('Severino da Silva');
    }

    /**
     * Testa exibição do formulário de criação.
     */
    public function test_usuario_pode_visualizar_formulario_de_cadastro(): void
    {
        $this->actingAs($this->user)
            ->get(route('pacientes.create'))
            ->assertOk()
            ->assertSee('Cadastrar Novo Paciente')
            ->assertSee('UBS Central');
    }

    /**
     * Testa cadastro de novo paciente com sucesso.
     */
    public function test_usuario_pode_cadastrar_novo_paciente_com_sucesso(): void
    {
        $dados = [
            'nome_completo' => 'Benedito dos Santos',
            'cpf' => '529.982.247-25',
            'cns' => '700000000000001',
            'data_nascimento' => '1975-03-12',
            'sexo' => 'M',
            'ubs_id' => $this->ubs->id,
            'nome_acs' => 'Agente Zé',
            'telefone_1' => '(82) 99999-1234',
            'telefone_2' => '(82) 3333-4444',
            'endereco' => 'Rua do Sol, 45, Centro',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('pacientes.store'), $dados);

        $paciente = Paciente::where('cpf', '52998224725')->first();
        $this->assertNotNull($paciente);

        $response->assertRedirect(route('pacientes.show', $paciente));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pacientes', [
            'nome_completo' => 'Benedito dos Santos',
            'cpf' => '52998224725',
            'cns' => '700000000000001',
            'nome_acs' => 'Agente Zé',
        ]);
    }

    /**
     * Testa exibição da página de prontuário (show).
     */
    public function test_usuario_pode_visualizar_prontuario_do_paciente(): void
    {
        $paciente = Paciente::create([
            'nome_completo' => 'Ana Clara Medeiros',
            'cpf' => '52998224725',
            'data_nascimento' => '1995-10-20',
            'sexo' => 'F',
            'telefone_1' => '82987654321',
            'ubs_id' => $this->ubs->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('pacientes.show', $paciente))
            ->assertOk()
            ->assertSee('Ana Clara Medeiros')
            ->assertSee('Prontuário #');
    }

    /**
     * Testa edição e atualização dos dados do paciente.
     */
    public function test_usuario_pode_atualizar_dados_do_paciente(): void
    {
        $paciente = Paciente::create([
            'nome_completo' => 'Nome Antigo',
            'cpf' => '52998224725',
            'data_nascimento' => '1990-01-01',
            'sexo' => 'M',
            'telefone_1' => '82999990000',
            'ubs_id' => $this->ubs->id,
        ]);

        $dadosAtualizados = [
            'nome_completo' => 'Nome Atualizado Perfeito',
            'cpf' => '529.982.247-25',
            'cns' => '',
            'data_nascimento' => '1990-01-01',
            'sexo' => 'M',
            'ubs_id' => $this->ubs->id,
            'telefone_1' => '(82) 98888-7777',
            'endereco' => 'Novo Endereço 123',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('pacientes.update', $paciente), $dadosAtualizados);

        $response->assertRedirect(route('pacientes.show', $paciente));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pacientes', [
            'id' => $paciente->id,
            'nome_completo' => 'Nome Atualizado Perfeito',
            'cns' => null,
            'endereco' => 'Novo Endereço 123',
        ]);
    }

    /**
     * Testa exclusão com sucesso de paciente sem agendamentos nem demanda pendente.
     */
    public function test_usuario_pode_excluir_paciente_sem_agendamentos(): void
    {
        $paciente = Paciente::create([
            'nome_completo' => 'Paciente Para Excluir',
            'cpf' => '52998224725',
            'data_nascimento' => '1992-04-10',
            'sexo' => 'M',
            'telefone_1' => '82988880000',
            'ubs_id' => $this->ubs->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('pacientes.destroy', $paciente));

        $response->assertRedirect(route('pacientes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('pacientes', ['id' => $paciente->id]);
    }

    /**
     * Garante que paciente com agendamentos cadastrados NÃO pode ser excluído.
     */
    public function test_nao_permite_excluir_paciente_com_historico_de_agendamentos(): void
    {
        $paciente = Paciente::create([
            'nome_completo' => 'Paciente Com Agenda',
            'cpf' => '52998224725',
            'data_nascimento' => '1985-06-15',
            'sexo' => 'F',
            'telefone_1' => '82988880000',
            'ubs_id' => $this->ubs->id,
        ]);

        $esp = Especialidade::create([
            'nome' => 'Endodontia',
            'descricao' => 'Canal',
            'tempo_medio_minutos' => 60,
        ]);

        $dentista = Dentista::create([
            'nome_completo' => 'Dr. Especialista',
            'cro' => 'CRO-12345',
            'telefone' => '82999998888',
            'especialidade_id' => $esp->id,
            'status_ativo' => true,
        ]);

        Agendamento::create([
            'paciente_id' => $paciente->id,
            'dentista_id' => $dentista->id,
            'especialidade_id' => $esp->id,
            'user_id' => $this->user->id,
            'data_agendamento' => now()->format('Y-m-d'),
            'turno' => 'manha',
            'tipo' => 'normal',
            'status' => 'agendado',
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('pacientes.destroy', $paciente));

        $response->assertRedirect(route('pacientes.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('pacientes', ['id' => $paciente->id]);
    }

    /**
     * Garante que paciente com demanda reprimida aguardando NÃO pode ser excluído.
     */
    public function test_nao_permite_excluir_paciente_com_demanda_reprimida_aguardando(): void
    {
        $paciente = Paciente::create([
            'nome_completo' => 'Paciente Em Fila',
            'cpf' => '52998224725',
            'data_nascimento' => '1985-06-15',
            'sexo' => 'M',
            'telefone_1' => '82988880000',
            'ubs_id' => $this->ubs->id,
        ]);

        $esp = Especialidade::create([
            'nome' => 'Ortodontia',
            'descricao' => 'Aparelho',
            'tempo_medio_minutos' => 45,
        ]);

        DemandaReprimida::create([
            'paciente_id' => $paciente->id,
            'especialidade_id' => $esp->id,
            'turno_preferencial' => 'qualquer',
            'prioridade' => 'normal',
            'status' => 'aguardando',
            'data_solicitacao' => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('pacientes.destroy', $paciente));

        $response->assertRedirect(route('pacientes.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('pacientes', ['id' => $paciente->id]);
    }
}
