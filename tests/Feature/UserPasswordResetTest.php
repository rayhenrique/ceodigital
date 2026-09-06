<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Auditoria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa que um administrador pode redefinir a senha de outro usuário.
     */
    public function test_administrador_pode_redefinir_senha_de_outro_usuario(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status_ativo' => true,
        ]);

        $operador = User::factory()->create([
            'role' => 'operador',
            'status_ativo' => true,
            'password' => Hash::make('senhaAntiga123'),
        ]);

        $response = $this->actingAs($admin)->post(route('users.reset-password', $operador), [
            'password' => 'NovaSenhaSegura@2026',
            'password_confirmation' => 'NovaSenhaSegura@2026',
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $operador->refresh();
        $this->assertTrue(Hash::check('NovaSenhaSegura@2026', $operador->password));
    }

    /**
     * Testa que o usuário consegue autenticar no login com a nova senha redefinida.
     */
    public function test_usuario_consegue_fazer_login_com_a_nova_senha(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status_ativo' => true,
        ]);

        $operador = User::factory()->create([
            'email' => 'operador.teste@saude.gov.br',
            'role' => 'operador',
            'status_ativo' => true,
            'password' => Hash::make('senhaAntiga123'),
        ]);

        // Admin redefine a senha
        $this->actingAs($admin)->post(route('users.reset-password', $operador), [
            'password' => 'SenhaResetada@123',
            'password_confirmation' => 'SenhaResetada@123',
        ]);

        // Faz logout do admin para simular visitante
        auth()->logout();

        // Operador tenta login com a senha antiga (deve falhar)
        $this->post(route('login'), [
            'email' => 'operador.teste@saude.gov.br',
            'password' => 'senhaAntiga123',
        ])->assertSessionHasErrors('email');

        // Operador tenta login com a nova senha (deve ter sucesso)
        $loginResponse = $this->post(route('login'), [
            'email' => 'operador.teste@saude.gov.br',
            'password' => 'SenhaResetada@123',
        ]);

        $loginResponse->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($operador);
    }

    /**
     * Testa validação: exige confirmação idêntica e no mínimo 8 caracteres.
     */
    public function test_validacao_rejeita_senha_curta_ou_sem_confirmacao(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status_ativo' => true,
        ]);

        $operador = User::factory()->create([
            'role' => 'operador',
            'status_ativo' => true,
        ]);

        // Senha com menos de 8 caracteres
        $responseCurta = $this->actingAs($admin)->post(route('users.reset-password', $operador), [
            'password' => '12345',
            'password_confirmation' => '12345',
        ]);
        $responseCurta->assertSessionHasErrors('password');

        // Confirmação diferente
        $responseDiferente = $this->actingAs($admin)->post(route('users.reset-password', $operador), [
            'password' => 'NovaSenha@2026',
            'password_confirmation' => 'OutraSenha@2026',
        ]);
        $responseDiferente->assertSessionHasErrors('password');
    }

    /**
     * Testa que operador comum não tem permissão para redefinir senhas (403 Forbidden).
     */
    public function test_operador_comum_e_bloqueado_de_redefinir_senhas(): void
    {
        $operador1 = User::factory()->create([
            'role' => 'operador',
            'status_ativo' => true,
        ]);

        $operador2 = User::factory()->create([
            'role' => 'operador',
            'status_ativo' => true,
        ]);

        $response = $this->actingAs($operador1)->post(route('users.reset-password', $operador2), [
            'password' => 'TentativaHacker@2026',
            'password_confirmation' => 'TentativaHacker@2026',
        ]);

        $response->assertForbidden();
    }
}
