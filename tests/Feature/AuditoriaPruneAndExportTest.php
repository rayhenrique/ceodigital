<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Auditoria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditoriaPruneAndExportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Garante que operadores comuns não têm acesso à exportação nem ao expurgo.
     */
    public function test_operador_nao_pode_acessar_exportacao_ou_expurgo_de_auditoria(): void
    {
        $operador = User::factory()->create([
            'role' => 'operador',
            'status_ativo' => true,
        ]);

        $this->actingAs($operador)
            ->get(route('auditorias.exportar'))
            ->assertForbidden();

        $this->actingAs($operador)
            ->post(route('auditorias.expurgar'), [
                'dias' => 180,
                'confirmacao' => 'EXPURGAR',
            ])
            ->assertForbidden();
    }

    /**
     * Testa que administrador consegue baixar o arquivo CSV com cabeçalhos e registros formatados.
     */
    public function test_administrador_pode_exportar_csv_de_auditoria(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status_ativo' => true,
        ]);

        Auditoria::create([
            'user_id' => $admin->id,
            'acao' => 'CRIAR',
            'tabela_afetada' => 'pacientes',
            'registro_id' => 99,
            'dados_antigos' => null,
            'dados_novos' => ['nome' => 'Paciente Teste'],
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->get(route('auditorias.exportar'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        
        // Verifica se o stream contém dados
        $content = $response->streamedContent();
        $this->assertStringContainsString('Tabela Afetada', $content);
        $this->assertStringContainsString('pacientes', $content);
        $this->assertStringContainsString('CRIAR', $content);
    }

    /**
     * Testa que o expurgo manual falha caso a palavra de confirmação não seja exatamente 'EXPURGAR'.
     */
    public function test_expurgo_manual_valida_palavra_de_confirmacao(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status_ativo' => true,
        ]);

        $response = $this->actingAs($admin)
            ->from(route('auditorias.index'))
            ->post(route('auditorias.expurgar'), [
                'dias' => 180,
                'confirmacao' => 'qualquer-coisa',
            ]);

        $response->assertSessionHasErrors('confirmacao');
    }

    /**
     * Testa que o expurgo manual remove somente registros anteriores à quantidade de dias selecionada.
     */
    public function test_expurgo_manual_deleta_apenas_registros_anteriores_ao_prazo(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status_ativo' => true,
        ]);

        // Registro muito antigo (200 dias atrás)
        $antigo = Auditoria::create([
            'user_id' => $admin->id,
            'acao' => 'CRIAR',
            'tabela_afetada' => 'ubs',
            'registro_id' => 1,
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(200),
        ]);

        // Registro recente (10 dias atrás)
        $recente = Auditoria::create([
            'user_id' => $admin->id,
            'acao' => 'ATUALIZAR',
            'tabela_afetada' => 'ubs',
            'registro_id' => 2,
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(10),
        ]);

        $response = $this->actingAs($admin)
            ->from(route('auditorias.index'))
            ->post(route('auditorias.expurgar'), [
                'dias' => 180,
                'confirmacao' => 'EXPURGAR',
            ]);

        $response->assertRedirect(route('auditorias.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('auditorias', ['id' => $antigo->id]);
        $this->assertDatabaseHas('auditorias', ['id' => $recente->id]);
    }

    /**
     * Testa o comando artisan audit:purge no terminal.
     */
    public function test_artisan_audit_purge_remove_registros_antigos(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $antigo = Auditoria::create([
            'user_id' => $admin->id,
            'acao' => 'CRIAR',
            'tabela_afetada' => 'ubs',
            'registro_id' => 1,
            'created_at' => now()->subDays(100),
        ]);

        $recente = Auditoria::create([
            'user_id' => $admin->id,
            'acao' => 'CRIAR',
            'tabela_afetada' => 'ubs',
            'registro_id' => 2,
            'created_at' => now()->subDays(10),
        ]);

        $this->artisan('audit:purge', ['--days' => 90])
            ->expectsOutputToContain('registros de auditoria anteriores a')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('auditorias', ['id' => $antigo->id]);
        $this->assertDatabaseHas('auditorias', ['id' => $recente->id]);
    }

    /**
     * Testa o método prunable() do model Auditoria utilizado pelo comando model:prune do Laravel.
     */
    public function test_auditoria_model_prunable_query(): void
    {
        $diasConfig = config('audit.retention_days', 180);

        $antigo = Auditoria::create([
            'acao' => 'CRIAR',
            'tabela_afetada' => 'ubs',
            'registro_id' => 1,
            'created_at' => now()->subDays($diasConfig + 10),
        ]);

        $recente = Auditoria::create([
            'acao' => 'CRIAR',
            'tabela_afetada' => 'ubs',
            'registro_id' => 2,
            'created_at' => now()->subDays(10),
        ]);

        $prunableIds = (new Auditoria())->prunable()->pluck('id')->toArray();

        $this->assertContains($antigo->id, $prunableIds);
        $this->assertNotContains($recente->id, $prunableIds);
    }
}
