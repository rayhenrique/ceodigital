<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Paciente;
use App\Models\Ubs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PacienteValidationTest extends TestCase
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
            'nome' => 'UBS Centro',
            'endereco' => 'Rua Principal, 100',
            'diretor' => 'Dr. Gestor',
            'contato' => '82999990000',
        ]);
    }

    /**
     * Testa rejeição de CPF com dígitos verificadores matematicamente inválidos.
     */
    public function test_rejeita_cpf_com_digitos_invalidos(): void
    {
        $dados = [
            'nome_completo' => 'João da Silva',
            'cpf' => '111.222.333-44', // CPF matematicamente inválido
            'data_nascimento' => '1990-05-15',
            'sexo' => 'M',
            'telefone_1' => '82999998888',
            'ubs_id' => $this->ubs->id,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('pacientes.store'), $dados);

        $response->assertSessionHasErrors(['cpf']);
        $this->assertDatabaseMissing('pacientes', ['nome_completo' => 'João da Silva']);
    }

    /**
     * Testa rejeição de CPF duplicado na base de dados.
     */
    public function test_rejeita_cpf_duplicado(): void
    {
        // CPF válido: 52998224725
        Paciente::create([
            'nome_completo' => 'Maria Primeira',
            'cpf' => '52998224725',
            'data_nascimento' => '1985-02-10',
            'sexo' => 'F',
            'telefone_1' => '82988887777',
            'ubs_id' => $this->ubs->id,
        ]);

        $dadosDuplicados = [
            'nome_completo' => 'Maria Segunda',
            'cpf' => '529.982.247-25', // Mesmo CPF com pontuação
            'data_nascimento' => '1992-08-20',
            'sexo' => 'F',
            'telefone_1' => '82977776666',
            'ubs_id' => $this->ubs->id,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('pacientes.store'), $dadosDuplicados);

        $response->assertSessionHasErrors(['cpf']);
        $this->assertDatabaseMissing('pacientes', ['nome_completo' => 'Maria Segunda']);
    }

    /**
     * Testa persistência com CPF válido e sua higienização para 11 dígitos numéricos.
     */
    public function test_persiste_paciente_com_cpf_valido_e_sanitiza(): void
    {
        // CPF válido gerado matematicamente: 52998224725
        $dadosValidos = [
            'nome_completo' => 'Carlos Valido dos Santos',
            'cpf' => '529.982.247-25',
            'data_nascimento' => '1988-12-01',
            'sexo' => 'M',
            'telefone_1' => '82991112233',
            'ubs_id' => $this->ubs->id,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('pacientes.store'), $dadosValidos);

        $response->assertRedirect();
        $this->assertDatabaseHas('pacientes', [
            'nome_completo' => 'Carlos Valido dos Santos',
            'cpf' => '52998224725', // Armazenado sem máscara
        ]);
    }
}
