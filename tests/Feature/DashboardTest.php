<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\Dentista;
use App\Models\Especialidade;
use App\Models\Paciente;
use App\Models\Ubs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Dentista $dentista;
    private Especialidade $especialidade;
    private Paciente $paciente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $ubs = Ubs::create([
            'nome' => 'UBS Centro',
            'codigo_cnes' => '1234567',
            'ativo' => true,
        ]);

        $this->especialidade = Especialidade::create([
            'nome' => 'Endodontia',
            'descricao' => 'Tratamento de canal',
            'ativo' => true,
        ]);

        $this->dentista = Dentista::create([
            'nome_completo' => 'Dr. Carlos Alberto',
            'cro' => '12345-PB',
            'especialidade_id' => $this->especialidade->id,
            'status_ativo' => true,
        ]);

        $this->paciente = Paciente::create([
            'nome_completo' => 'Maria de Lourdes',
            'cpf' => '12345678901',
            'data_nascimento' => '1985-05-10',
            'sexo' => 'F',
            'cns' => '700000000000001',
            'ubs_id' => $ubs->id,
            'telefone_1' => '83999999999',
        ]);
    }

    public function test_dashboard_renders_successfully_with_agendamentos(): void
    {
        $agendamento = Agendamento::create([
            'paciente_id' => $this->paciente->id,
            'dentista_id' => $this->dentista->id,
            'especialidade_id' => $this->especialidade->id,
            'user_id' => $this->admin->id,
            'data_agendamento' => now()->toDateString(),
            'turno' => 'manha',
            'tipo' => 'normal',
            'status' => 'agendado',
        ]);

        // Testa o accessor retrocompatível no Model
        $this->assertNotNull($agendamento->data);
        $this->assertEquals($agendamento->data_agendamento->toDateString(), $agendamento->data->toDateString());

        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Maria de Lourdes');
        $response->assertSee('Dr. Carlos Alberto');
        $response->assertSee('Endodontia');
        $response->assertSee(route('agenda.index', [
            'data' => $agendamento->data_agendamento->format('Y-m-d'),
            'dentista_id' => $agendamento->dentista_id,
        ]));
    }

    public function test_dashboard_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }
}
