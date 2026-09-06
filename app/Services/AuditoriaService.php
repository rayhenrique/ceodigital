<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Auditoria;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditoriaService
{
    /**
     * Constrói a consulta de auditorias aplicando os filtros informados.
     *
     * @param array<string, mixed> $filtros
     * @return Builder<Auditoria>
     */
    public function buildQuery(array $filtros): Builder
    {
        $tabela = $filtros['tabela_afetada'] ?? null;
        $acao = $filtros['acao'] ?? null;
        $userId = $filtros['user_id'] ?? null;
        $dataInicio = $filtros['data_inicio'] ?? null;
        $dataFim = $filtros['data_fim'] ?? null;

        /** @var Builder<Auditoria> $query */
        $query = Auditoria::query();

        $query->with('user')
            ->when($tabela, fn (Builder $q) => $q->where('tabela_afetada', $tabela))
            ->when($acao, fn (Builder $q) => $q->where('acao', 'like', "%{$acao}%"))
            ->when($userId, fn (Builder $q) => $q->where('user_id', (int) $userId))
            ->when($dataInicio, fn (Builder $q) => $q->whereDate('created_at', '>=', $dataInicio))
            ->when($dataFim, fn (Builder $q) => $q->whereDate('created_at', '<=', $dataFim))
            ->orderBy('created_at', 'desc');

        return $query;
    }

    /**
     * Executa o expurgo de registros de auditoria anteriores ao período especificado em dias.
     */
    public function expurgar(int $dias): int
    {
        $dataLimite = now()->subDays($dias);

        return DB::transaction(function () use ($dataLimite): int {
            return Auditoria::where('created_at', '<=', $dataLimite)->delete();
        });
    }

    /**
     * Gera resposta de download com streaming do CSV para evitar consumo excessivo de memória.
     *
     * @param array<string, mixed> $filtros
     */
    public function streamCsv(array $filtros): StreamedResponse
    {
        $filename = 'auditoria_' . now()->format('Ymd_His') . '.csv';

        $query = $this->buildQuery($filtros);

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            // Escreve o BOM UTF-8 para compatibilidade perfeita com Microsoft Excel
            fwrite($handle, "\xEF\xBB\xBF");

            // Cabeçalho das colunas do CSV
            fputcsv($handle, [
                'ID',
                'Data/Hora',
                'Operador',
                'Ação',
                'Tabela Afetada',
                'Registro ID',
                'IP Origem',
                'Detalhes Anteriores',
                'Detalhes Novos',
            ], ';');

            $query->chunk(500, function ($registros) use ($handle): void {
                foreach ($registros as $audit) {
                    fputcsv($handle, [
                        $audit->id,
                        $audit->created_at?->format('d/m/Y H:i:s'),
                        $audit->user?->name ?? 'Sistema / Cron',
                        strtoupper((string) $audit->acao),
                        $audit->tabela_afetada,
                        $audit->registro_id,
                        $audit->ip_address ?? '-',
                        $audit->dados_anteriores ? json_encode($audit->dados_anteriores, JSON_UNESCAPED_UNICODE) : '',
                        $audit->dados_novos ? json_encode($audit->dados_novos, JSON_UNESCAPED_UNICODE) : '',
                    ], ';');
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
