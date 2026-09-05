<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa redirecionamento de usuário não autenticado para o login.
     */
    public function test_visitante_e_redirecionado_para_login(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));

        $responseAgenda = $this->get(route('agenda.index'));
        $responseAgenda->assertRedirect(route('login'));
    }

    /**
     * Testa que usuário com perfil operador não acessa rotas restritas de administração (403 Forbidden).
     */
    public function test_operador_e_bloqueado_em_rotas_administrativas(): void
    {
        $operador = User::factory()->create([
            'role' => 'operador',
            'status_ativo' => true,
        ]);

        // Gestão de Usuários
        $responseUsers = $this->actingAs($operador)->get(route('users.index'));
        $responseUsers->assertForbidden();

        // Trilha de Auditoria
        $responseAuditoria = $this->actingAs($operador)->get(route('auditorias.index'));
        $responseAuditoria->assertForbidden();

        // Cadastro de Dentista
        $responseDentista = $this->actingAs($operador)->get(route('dentistas.create'));
        $responseDentista->assertForbidden();

        // Cadastro de Especialidade
        $responseEspecialidade = $this->actingAs($operador)->get(route('especialidades.create'));
        $responseEspecialidade->assertForbidden();
    }

    /**
     * Testa que o usuário com perfil administrador possui acesso irrestrito às rotas restritas.
     */
    public function test_administrador_possui_acesso_a_rotas_administrativas(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status_ativo' => true,
        ]);

        $responseUsers = $this->actingAs($admin)->get(route('users.index'));
        $responseUsers->assertOk();

        $responseAuditoria = $this->actingAs($admin)->get(route('auditorias.index'));
        $responseAuditoria->assertOk();

        $responseDentista = $this->actingAs($admin)->get(route('dentistas.create'));
        $responseDentista->assertOk();

        $responseEspecialidade = $this->actingAs($admin)->get(route('especialidades.create'));
        $responseEspecialidade->assertOk();
    }
}
