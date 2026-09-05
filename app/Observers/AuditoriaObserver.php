<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Auditoria;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditoriaObserver
{
    /**
     * Atributos que devem ser mascarados ou ignorados por segurança nos registros de auditoria.
     *
     * @var list<string>
     */
    protected array $camposProtegidos = [
        'password',
        'remember_token',
    ];

    /**
     * Disparado após a criação de um registro.
     */
    public function created(Model $model): void
    {
        $this->gravarAuditoria(
            model: $model,
            acao: "{$model->getTable()}.criado",
            dadosAnteriores: null,
            dadosNovos: $this->filtrarAtributos($model->getAttributes())
        );
    }

    /**
     * Disparado após a atualização de um registro.
     */
    public function updated(Model $model): void
    {
        $alteracoes = $model->getChanges();
        unset($alteracoes['updated_at']);

        if (empty($alteracoes)) {
            return;
        }

        $dadosAnteriores = [];
        $dadosNovos = [];

        foreach ($alteracoes as $campo => $novoValor) {
            if (in_array($campo, $this->camposProtegidos, true)) {
                continue;
            }
            $dadosAnteriores[$campo] = $model->getOriginal($campo);
            $dadosNovos[$campo] = $novoValor;
        }

        $this->gravarAuditoria(
            model: $model,
            acao: "{$model->getTable()}.atualizado",
            dadosAnteriores: $dadosAnteriores,
            dadosNovos: $dadosNovos
        );
    }

    /**
     * Disparado após a exclusão de um registro.
     */
    public function deleted(Model $model): void
    {
        $this->gravarAuditoria(
            model: $model,
            acao: "{$model->getTable()}.excluido",
            dadosAnteriores: $this->filtrarAtributos($model->getAttributes()),
            dadosNovos: null
        );
    }

    /**
     * Registra o evento na tabela auditorias.
     *
     * @param array<string, mixed>|null $dadosAnteriores
     * @param array<string, mixed>|null $dadosNovos
     */
    protected function gravarAuditoria(
        Model $model,
        string $acao,
        ?array $dadosAnteriores,
        ?array $dadosNovos
    ): void {
        Auditoria::create([
            'user_id' => Auth::id(),
            'acao' => $acao,
            'tabela_afetada' => $model->getTable(),
            'registro_id' => (int) $model->getKey(),
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos' => $dadosNovos,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * Remove atributos sensíveis dos dados a serem persistidos no log.
     *
     * @param array<string, mixed> $atributos
     * @return array<string, mixed>
     */
    protected function filtrarAtributos(array $atributos): array
    {
        foreach ($this->camposProtegidos as $campo) {
            if (array_key_exists($campo, $atributos)) {
                $atributos[$campo] = '********';
            }
        }

        return $atributos;
    }
}
