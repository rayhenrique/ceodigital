<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Paciente;
use App\Models\Ubs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UbsCrudTest extends TestCase
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
     * Testa a listagem de UBSs.
     */
    public function test_pode_listar_unidades_basicas_de_saude(): void
    {
        Ubs::create([
            'nome' => 'UBS Centro',
            'endereco' => 'Rua Principal, 100',
            'diretor' => 'Dr. Gestor',
            'contato' => '82999990000',
        ]);

        $response = $this->actingAs($this->admin)->get(route('ubs.index'));

        $response->assertOk();
        $response->assertSee('UBS Centro');
    }

    /**
     * Testa o cadastro de uma nova UBS.
     */
    public function test_pode_cadastrar_nova_ubs(): void
    {
        $dados = [
            'nome' => 'UBS Bairro Novo',
            'endereco' => 'Rua das Flores, 123',
            'diretor' => 'Dra. Maria Santos',
            'contato' => '(82) 3333-4444',
        ];

        $response = $this->actingAs($this->admin)->post(route('ubs.store'), $dados);

        $response->assertRedirect(route('ubs.index'));
        $this->assertDatabaseHas('ubs', ['nome' => 'UBS Bairro Novo']);
    }

    /**
     * Testa a atualização de uma UBS existente.
     */
    public function test_pode_atualizar_dados_da_ubs(): void
    {
        $ubs = Ubs::create([
            'nome' => 'UBS Antiga',
            'endereco' => 'Rua Velha, 50',
            'diretor' => 'Dr. Antigo',
            'contato' => '8233332222',
        ]);

        $response = $this->actingAs($this->admin)->put(route('ubs.update', $ubs), [
            'nome' => 'UBS Reformada',
            'endereco' => 'Avenida Principal, 456',
            'diretor' => 'Dr. João Silva',
            'contato' => '(82) 9999-8888',
        ]);

        $response->assertRedirect(route('ubs.index'));
        $this->assertDatabaseHas('ubs', [
            'id' => $ubs->id,
            'nome' => 'UBS Reformada',
        ]);
    }

    /**
     * Testa a exclusão de uma UBS sem pacientes vinculados (permitida).
     */
    public function test_pode_excluir_ubs_sem_pacientes_vinculados(): void
    {
        $ubs = Ubs::create([
            'nome' => 'UBS Temporária',
            'endereco' => 'Rua Provisória, 1',
            'diretor' => 'Coordenação',
            'contato' => '8232110000',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('ubs.destroy', $ubs));

        $response->assertRedirect(route('ubs.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('ubs', ['id' => $ubs->id]);
    }

    /**
     * Testa que a exclusão de UBS com pacientes vinculados é bloqueada com mensagem de erro.
     */
    public function test_bloqueia_exclusao_de_ubs_com_pacientes_vinculados(): void
    {
        $ubs = Ubs::create([
            'nome' => 'UBS com Pacientes',
            'endereco' => 'Rua Cheia, 99',
            'diretor' => 'Diretoria',
            'contato' => '8232119999',
        ]);

        Paciente::create([
            'ubs_id' => $ubs->id,
            'nome_completo' => 'Paciente Teste UBS',
            'cpf' => '01234567890',
            'cns' => '700000000000001',
            'data_nascimento' => '1990-01-01',
            'sexo' => 'M',
            'telefone_1' => '82988887777',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('ubs.destroy', $ubs));

        $response->assertRedirect(route('ubs.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('ubs', ['id' => $ubs->id]);
    }
}
