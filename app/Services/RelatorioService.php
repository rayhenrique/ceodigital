<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Agendamento;
use App\Models\DemandaReprimida;
use App\Models\Ubs;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RelatorioService
{
    /**
     * Gera o relatório gerencial de absenteísmo (faltas) por período, UBS e especialidade.
     *
     * @param array<string, mixed> $filtros
     * @return array<string, mixed>
     */
    public function obterRelatorioAbsenteismo(array $filtros = []): array
    {
        $dataInicio = $filtros['data_inicio'] ?? now()->startOfMonth()->toDateString();
        $dataFim = $filtros['data_fim'] ?? now()->toDateString();

        $queryBase = Agendamento::query()
            ->with(['paciente.ubs', 'dentista', 'especialidade'])
            ->whereBetween('data_agendamento', [$dataInicio, $dataFim])
            ->whereNotIn('status', ['cancelado']);

        if (! empty($filtros['especialidade_id'])) {
            $queryBase->where('especialidade_id', (int) $filtros['especialidade_id']);
        }

        if (! empty($filtros['ubs_id'])) {
            $queryBase->whereHas('paciente', function (Builder $q) use ($filtros) {
                $q->where('ubs_id', (int) $filtros['ubs_id']);
            });
        }

        $totalAgendamentos = (clone $queryBase)->count();
        $totalFaltas = (clone $queryBase)->where('status', 'falta')->count();
        $taxaAbsenteismoGeral = $totalAgendamentos > 0
            ? round(($totalFaltas / $totalAgendamentos) * 100, 2)
            : 0.0;

        // Absenteísmo consolidado por UBS
        $porUbs = (clone $queryBase)
            ->join('pacientes', 'agendamentos.paciente_id', '=', 'pacientes.id')
            ->join('ubs', 'pacientes.ubs_id', '=', 'ubs.id')
            ->select(
                'ubs.id as ubs_id',
                'ubs.nome as ubs_nome',
                DB::raw('COUNT(agendamentos.id) as total_agendados'),
                DB::raw("SUM(CASE WHEN agendamentos.status = 'falta' THEN 1 ELSE 0 END) as total_faltas")
            )
            ->groupBy('ubs.id', 'ubs.nome')
            ->get()
            ->map(function ($item) {
                $taxa = $item->total_agendados > 0
                    ? round(((int) $item->total_faltas / (int) $item->total_agendados) * 100, 2)
                    : 0.0;
                return [
                    'ubs_id' => $item->ubs_id,
                    'ubs_nome' => $item->ubs_nome,
                    'total_agendados' => (int) $item->total_agendados,
                    'total_faltas' => (int) $item->total_faltas,
                    'taxa_absenteismo' => $taxa,
                ];
            });

        // Relação nominal de faltas
        $faltasNominais = (clone $queryBase)
            ->where('status', 'falta')
            ->orderBy('data_agendamento', 'desc')
            ->get();

        return [
            'periodo' => [
                'inicio' => $dataInicio,
                'fim' => $dataFim,
            ],
            'total_agendamentos' => $totalAgendamentos,
            'total_faltas' => $totalFaltas,
            'taxa_absenteismo_geral' => $taxaAbsenteismoGeral,
            'consolidado_por_ubs' => $porUbs,
            'faltas_nominais' => $faltasNominais,
        ];
    }

    /**
     * Gera o relatório gerencial de produção de atendimentos concluídos por dentista e especialidade.
     *
     * @param array<string, mixed> $filtros
     * @return array<string, mixed>
     */
    public function obterRelatorioProducao(array $filtros = []): array
    {
        $dataInicio = $filtros['data_inicio'] ?? now()->startOfMonth()->toDateString();
        $dataFim = $filtros['data_fim'] ?? now()->toDateString();

        $query = Agendamento::query()
            ->join('dentistas', 'agendamentos.dentista_id', '=', 'dentistas.id')
            ->join('especialidades', 'agendamentos.especialidade_id', '=', 'especialidades.id')
            ->whereBetween('agendamentos.data_agendamento', [$dataInicio, $dataFim])
            ->where('agendamentos.status', 'concluido');

        if (! empty($filtros['dentista_id'])) {
            $query->where('agendamentos.dentista_id', (int) $filtros['dentista_id']);
        }

        if (! empty($filtros['especialidade_id'])) {
            $query->where('agendamentos.especialidade_id', (int) $filtros['especialidade_id']);
        }

        $totalGeral = (clone $query)->count();

        // Produção agrupada por profissional
        $producaoPorDentista = (clone $query)
            ->select(
                'dentistas.id as dentista_id',
                'dentistas.nome_completo as dentista_nome',
                'dentistas.cro',
                'especialidades.nome as especialidade_nome',
                DB::raw('COUNT(agendamentos.id) as total_concluidos'),
                DB::raw("SUM(CASE WHEN agendamentos.tipo = 'normal' THEN 1 ELSE 0 END) as total_normais"),
                DB::raw("SUM(CASE WHEN agendamentos.tipo = 'encaixe' THEN 1 ELSE 0 END) as total_encaixes"),
                DB::raw("SUM(CASE WHEN agendamentos.tipo = 'espontanea' THEN 1 ELSE 0 END) as total_espontaneas")
            )
            ->groupBy('dentistas.id', 'dentistas.nome_completo', 'dentistas.cro', 'especialidades.nome')
            ->orderByDesc('total_concluidos')
            ->get();

        return [
            'periodo' => [
                'inicio' => $dataInicio,
                'fim' => $dataFim,
            ],
            'total_concluidos' => $totalGeral,
            'producao_por_dentista' => $producaoPorDentista,
        ];
    }

    /**
     * Gera o relatório gerencial da demanda reprimida (fila de espera) com tempo médio de espera.
     *
     * @param array<string, mixed> $filtros
     * @return array<string, mixed>
     */
    public function obterRelatorioDemandaReprimida(array $filtros = []): array
    {
        $query = DemandaReprimida::query()
            ->with(['paciente.ubs', 'especialidade'])
            ->where('status', 'aguardando');

        if (! empty($filtros['especialidade_id'])) {
            $query->where('especialidade_id', (int) $filtros['especialidade_id']);
        }

        if (! empty($filtros['prioridade'])) {
            $query->where('prioridade', $filtros['prioridade']);
        }

        $totalAguardando = (clone $query)->count();
        $totalUrgentes = (clone $query)->where('prioridade', 'urgente')->count();
        $totalNormais = (clone $query)->where('prioridade', 'normal')->count();

        // Cálculo de tempo médio de espera em dias
        $tempoMedioDias = (clone $query)
            ->selectRaw('AVG(DATEDIFF(CURRENT_DATE, data_solicitacao)) as media_dias')
            ->value('media_dias');

        // Consolidado por especialidade
        $porEspecialidade = (clone $query)
            ->join('especialidades', 'demanda_reprimida.especialidade_id', '=', 'especialidades.id')
            ->select(
                'especialidades.id as especialidade_id',
                'especialidades.nome as especialidade_nome',
                DB::raw('COUNT(demanda_reprimida.id) as total'),
                DB::raw("SUM(CASE WHEN demanda_reprimida.prioridade = 'urgente' THEN 1 ELSE 0 END) as urgentes")
            )
            ->groupBy('especialidades.id', 'especialidades.nome')
            ->get();

        $listaPacientes = (clone $query)
            ->orderByRaw("CASE WHEN prioridade = 'urgente' THEN 0 ELSE 1 END")
            ->orderBy('data_solicitacao', 'asc')
            ->get();

        return [
            'total_aguardando' => $totalAguardando,
            'total_urgentes' => $totalUrgentes,
            'total_normais' => $totalNormais,
            'tempo_medio_espera_dias' => $tempoMedioDias !== null ? round((float) $tempoMedioDias, 1) : 0,
            'por_especialidade' => $porEspecialidade,
            'lista_espera' => $listaPacientes,
        ];
    }

    /**
     * Agrega métricas executivas para o Dashboard operacional do dia corrente.
     *
     * @return array<string, mixed>
     */
    public function obterMetricasDashboard(): array
    {
        $hoje = now()->toDateString();
        $inicioMes = now()->startOfMonth()->toDateString();

        // Total e distribuição por turnos no dia
        $agendamentosHoje = Agendamento::where('data_agendamento', $hoje)
            ->whereNotIn('status', ['cancelado'])
            ->get();

        $totalHoje = $agendamentosHoje->count();
        $manhaHoje = $agendamentosHoje->where('turno', 'manha')->count();
        $tardeHoje = $agendamentosHoje->where('turno', 'tarde')->count();
        $noiteHoje = $agendamentosHoje->where('turno', 'noite')->count();

        // Métricas detalhadas de hoje
        $presentesHoje = $agendamentosHoje->where('status', 'presente')->count();
        $emAtendimentoHoje = $agendamentosHoje->where('status', 'em_atendimento')->count();
        $concluidosHoje = $agendamentosHoje->where('status', 'concluido')->count();
        $faltasHoje = $agendamentosHoje->where('status', 'falta')->count();
        $encaixesHoje = $agendamentosHoje->where('tipo', 'encaixe')->count();

        // Absenteísmo do mês corrente
        $agendadosMes = Agendamento::whereBetween('data_agendamento', [$inicioMes, $hoje])
            ->whereNotIn('status', ['cancelado'])
            ->count();

        $faltasMes = Agendamento::whereBetween('data_agendamento', [$inicioMes, $hoje])
            ->where('status', 'falta')
            ->count();

        $taxaAbsenteismoMes = $agendadosMes > 0
            ? round(($faltasMes / $agendadosMes) * 100, 1)
            : 0.0;

        // Pacientes em espera na demanda reprimida
        $totalEspera = DemandaReprimida::where('status', 'aguardando')->count();

        return [
            'total_hoje' => $totalHoje,
            'manha_hoje' => $manhaHoje,
            'tarde_hoje' => $tardeHoje,
            'noite_hoje' => $noiteHoje,
            'agendados_mes' => $agendadosMes,
            'faltas_mes' => $faltasMes,
            'taxa_absenteismo_mes' => $taxaAbsenteismoMes,
            'total_espera' => $totalEspera,
            'demanda_reprimida_total' => $totalEspera,
            'hoje' => [
                'total_agendados' => $totalHoje,
                'presentes' => $presentesHoje + $emAtendimentoHoje,
                'concluidos' => $concluidosHoje,
                'faltas' => $faltasHoje,
                'encaixes' => $encaixesHoje,
            ],
            'mes' => [
                'total' => $agendadosMes,
                'concluidos' => Agendamento::whereBetween('data_agendamento', [$inicioMes, $hoje])->where('status', 'concluido')->count(),
                'absenteismo_percentual' => $taxaAbsenteismoMes,
            ],
        ];
    }
}
