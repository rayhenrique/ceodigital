<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Agendamento;
use App\Models\DemandaReprimida;
use App\Models\Dentista;
use App\Models\DentistaGrade;
use DomainException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AgendamentoService
{
    /**
     * Realiza um agendamento normal respeitando a capacidade da grade do dentista.
     *
     * @param array<string, mixed> $dados
     * @throws DomainException
     */
    public function agendar(array $dados, int $userId): Agendamento
    {
        return DB::transaction(function () use ($dados, $userId): Agendamento {
            $data = Carbon::parse($dados['data_agendamento']);
            $diaSemana = $data->dayOfWeekIso;

            // Busca a capacidade padrão da grade do dentista
            $grade = DentistaGrade::where('dentista_id', $dados['dentista_id'])
                ->where('dia_semana', $diaSemana)
                ->where('turno', $dados['turno'])
                ->first();

            $capacidadeMaxima = $grade ? $grade->vagas_padrao : 8;

            // Contabiliza agendamentos normais ativos no turno
            $agendadosNoTurno = Agendamento::where('dentista_id', $dados['dentista_id'])
                ->whereDate('data_agendamento', $dados['data_agendamento'])
                ->where('turno', $dados['turno'])
                ->where('tipo', 'normal')
                ->whereNotIn('status', ['cancelado'])
                ->lockForUpdate()
                ->count();

            if ($agendadosNoTurno >= $capacidadeMaxima) {
                throw new DomainException(
                    "A capacidade padrão deste turno ({$capacidadeMaxima} vagas) já foi atingida. Para incluir este paciente, utilize a opção de Encaixe."
                );
            }

            return Agendamento::create([
                'paciente_id' => $dados['paciente_id'],
                'dentista_id' => $dados['dentista_id'],
                'especialidade_id' => $dados['especialidade_id'],
                'user_id' => $userId,
                'data_agendamento' => $dados['data_agendamento'],
                'turno' => $dados['turno'],
                'tipo' => 'normal',
                'status' => 'agendado',
                'observacao' => $dados['observacao'] ?? null,
            ]);
        });
    }

    /**
     * Realiza a inclusão extraordinária de um agendamento por encaixe (máximo 2 por turno).
     *
     * @param array<string, mixed> $dados
     * @throws DomainException
     */
    public function realizarEncaixe(array $dados, int $userId): Agendamento
    {
        return DB::transaction(function () use ($dados, $userId): Agendamento {
            $totalEncaixes = Agendamento::where('dentista_id', $dados['dentista_id'])
                ->whereDate('data_agendamento', $dados['data_agendamento'])
                ->where('turno', $dados['turno'])
                ->where('tipo', 'encaixe')
                ->whereNotIn('status', ['cancelado'])
                ->lockForUpdate()
                ->count();

            if ($totalEncaixes >= 2) {
                throw new DomainException('Limite de encaixes excedido. Permitido no máximo 2 encaixes por dentista no mesmo turno.');
            }

            $especialidadeId = $dados['especialidade_id'] ?? Dentista::where('id', $dados['dentista_id'])->value('especialidade_id');

            return Agendamento::create([
                'paciente_id' => $dados['paciente_id'],
                'dentista_id' => $dados['dentista_id'],
                'especialidade_id' => $especialidadeId,
                'user_id' => $userId,
                'data_agendamento' => $dados['data_agendamento'],
                'turno' => $dados['turno'],
                'tipo' => 'encaixe',
                'status' => 'agendado',
                'observacao' => $dados['observacao'] ?? ($dados['observacoes'] ?? 'Encaixe extraordinário de turno.'),
            ]);
        });
    }

    /**
     * Promove um registro da demanda reprimida (fila de espera) convertendo-o diretamente em agendamento.
     *
     * @param array<string, mixed> $dadosAgendamento
     * @throws DomainException
     */
    public function promoverDemandaReprimida(
        int $demandaReprimidaId,
        array $dadosAgendamento,
        int $userId
    ): Agendamento {
        return DB::transaction(function () use ($demandaReprimidaId, $dadosAgendamento, $userId): Agendamento {
            /** @var DemandaReprimida $demanda */
            $demanda = DemandaReprimida::where('id', $demandaReprimidaId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($demanda->status !== 'aguardando') {
                throw new DomainException(
                    "Apenas solicitações com status 'aguardando' podem ser promovidas para a agenda."
                );
            }

            $tipo = $dadosAgendamento['tipo'] ?? 'normal';

            if ($tipo === 'encaixe') {
                $totalEncaixes = Agendamento::where('dentista_id', $dadosAgendamento['dentista_id'])
                    ->whereDate('data_agendamento', $dadosAgendamento['data_agendamento'])
                    ->where('turno', $dadosAgendamento['turno'])
                    ->where('tipo', 'encaixe')
                    ->whereNotIn('status', ['cancelado'])
                    ->lockForUpdate()
                    ->count();

                if ($totalEncaixes >= 2) {
                    throw new DomainException('Limite de encaixes excedido. Permitido no máximo 2 encaixes por dentista no mesmo turno.');
                }
            } else {
                $data = Carbon::parse($dadosAgendamento['data_agendamento']);
                $diaSemana = $data->dayOfWeekIso;

                $grade = DentistaGrade::where('dentista_id', $dadosAgendamento['dentista_id'])
                    ->where('dia_semana', $diaSemana)
                    ->where('turno', $dadosAgendamento['turno'])
                    ->first();

                $capacidadeMaxima = $grade ? $grade->vagas_padrao : 8;

                $agendadosNoTurno = Agendamento::where('dentista_id', $dadosAgendamento['dentista_id'])
                    ->whereDate('data_agendamento', $dadosAgendamento['data_agendamento'])
                    ->where('turno', $dadosAgendamento['turno'])
                    ->where('tipo', 'normal')
                    ->whereNotIn('status', ['cancelado'])
                    ->lockForUpdate()
                    ->count();

                if ($agendadosNoTurno >= $capacidadeMaxima) {
                    throw new DomainException(
                        "A capacidade padrão deste turno ({$capacidadeMaxima} vagas) já foi atingida. Para incluir este paciente, utilize a opção de Encaixe."
                    );
                }
            }

            $agendamento = Agendamento::create([
                'paciente_id' => $demanda->paciente_id,
                'dentista_id' => $dadosAgendamento['dentista_id'],
                'especialidade_id' => $demanda->especialidade_id,
                'user_id' => $userId,
                'data_agendamento' => $dadosAgendamento['data_agendamento'],
                'turno' => $dadosAgendamento['turno'],
                'tipo' => $tipo,
                'status' => 'agendado',
                'observacao' => $dadosAgendamento['observacao'] ?? "Promovido da demanda reprimida em {$demanda->data_solicitacao->format('d/m/Y')}.",
            ]);

            // Atualiza status na fila de espera
            $demanda->update(['status' => 'agendado']);

            return $agendamento;
        });
    }

    /**
     * Atualiza o status de atendimento e fluxo de chegada do paciente no turno.
     *
     * @throws DomainException
     */
    public function atualizarStatusChegada(
        int $agendamentoId,
        string $status,
        ?string $horarioChegada = null
    ): Agendamento {
        return DB::transaction(function () use ($agendamentoId, $status, $horarioChegada): Agendamento {
            $statusValidos = ['agendado', 'presente', 'em_atendimento', 'concluido', 'falta', 'cancelado'];

            if (! in_array($status, $statusValidos, true)) {
                throw new DomainException("Status '{$status}' é inválido para o agendamento.");
            }

            /** @var Agendamento $agendamento */
            $agendamento = Agendamento::where('id', $agendamentoId)
                ->lockForUpdate()
                ->firstOrFail();

            $updateData = ['status' => $status];

            if ($status === 'presente') {
                $updateData['horario_chegada'] = $horarioChegada ?? now()->format('H:i:s');
            }

            $agendamento->update($updateData);

            return $agendamento->fresh();
        });
    }

    /**
     * Gera o mapa de ocupação e lotação mensal da agenda para visualização gerencial.
     *
     * @param int $ano
     * @param int $mes
     * @param int|null $especialidadeId
     * @param int|null $dentistaId
     * @param string|null $turno
     * @return array<string, mixed>
     */
    public function obterMapaMensal(
        int $ano,
        int $mes,
        ?int $especialidadeId = null,
        ?int $dentistaId = null,
        ?string $turno = null
    ): array {
        $dataInicioMes = Carbon::createFromDate($ano, $mes, 1)->startOfDay();
        $dataFimMes = $dataInicioMes->copy()->endOfMonth()->endOfDay();

        // 1. Consulta de grades padrão ativas para calcular capacidade
        $gradesQuery = DentistaGrade::query()
            ->whereHas('dentista', fn ($q) => $q->where('status_ativo', true))
            ->when($dentistaId, fn ($q) => $q->where('dentista_id', $dentistaId))
            ->when($especialidadeId, fn ($q) => $q->whereHas('dentista', fn ($sub) => $sub->where('especialidade_id', $especialidadeId)))
            ->when($turno, fn ($q) => $q->where('turno', $turno));

        // Capacidade por dia da semana (1 = Seg ... 7 = Dom)
        $capacidadePorDiaSemana = $gradesQuery
            ->select('dia_semana', DB::raw('SUM(vagas_padrao) as total_vagas'))
            ->groupBy('dia_semana')
            ->pluck('total_vagas', 'dia_semana')
            ->map(fn ($val): int => (int) $val)
            ->toArray();

        // 2. Consulta de agendamentos no mês filtrado
        $agendamentos = Agendamento::query()
            ->whereBetween('data_agendamento', [$dataInicioMes->toDateString(), $dataFimMes->toDateString()])
            ->whereNotIn('status', ['cancelado'])
            ->when($dentistaId, fn ($q) => $q->where('dentista_id', $dentistaId))
            ->when($especialidadeId, fn ($q) => $q->where('especialidade_id', $especialidadeId))
            ->when($turno, fn ($q) => $q->where('turno', $turno))
            ->get();

        $agendamentosPorData = $agendamentos->groupBy(fn ($item) => $item->data_agendamento->toDateString());

        // 3. Montar matriz de calendário com padding de semanas (Segunda a Domingo)
        $primeiroDiaGrade = $dataInicioMes->copy()->startOfWeek(Carbon::MONDAY);
        $ultimoDiaGrade = $dataFimMes->copy()->endOfWeek(Carbon::SUNDAY);

        $diasGrade = [];
        $cursor = $primeiroDiaGrade->copy();

        $capacidadeTotalMes = 0;
        $totalAgendadosMes = 0;
        $diasLotadosCount = 0;
        $totalConcluidos = 0;
        $totalFaltas = 0;

        while ($cursor->lte($ultimoDiaGrade)) {
            $dataStr = $cursor->toDateString();
            $diaSemanaIso = $cursor->dayOfWeekIso;
            $isMesAtual = $cursor->month === $mes;

            /** @var \Illuminate\Support\Collection<int, Agendamento> $agendamentosDia */
            $agendamentosDia = $agendamentosPorData->get($dataStr, collect());
            $totalAgendados = $agendamentosDia->count();
            $capacidadeDia = $capacidadePorDiaSemana[$diaSemanaIso] ?? 0;

            $concluidosDia = $agendamentosDia->where('status', 'concluido')->count();
            $faltasDia = $agendamentosDia->where('status', 'falta')->count();

            if ($isMesAtual) {
                $capacidadeTotalMes += $capacidadeDia;
                $totalAgendadosMes += $totalAgendados;
                $totalConcluidos += $concluidosDia;
                $totalFaltas += $faltasDia;
            }

            if ($capacidadeDia > 0) {
                $percentual = (int) round(($totalAgendados / $capacidadeDia) * 100);
                if ($percentual >= 100) {
                    $nivel = 'lotado';
                    $corBadge = 'bg-rose-100 text-rose-800 border-rose-200';
                    $corBarra = 'bg-rose-500';
                    $textoStatus = 'Lotado';
                    if ($isMesAtual) {
                        $diasLotadosCount++;
                    }
                } elseif ($percentual >= 80) {
                    $nivel = 'quase_cheio';
                    $corBadge = 'bg-amber-100 text-amber-800 border-amber-200';
                    $corBarra = 'bg-amber-500';
                    $textoStatus = 'Quase Cheio';
                } elseif ($percentual >= 50) {
                    $nivel = 'moderado';
                    $corBadge = 'bg-blue-100 text-blue-800 border-blue-200';
                    $corBarra = 'bg-blue-500';
                    $textoStatus = 'Moderado';
                } else {
                    $nivel = 'livre';
                    $corBadge = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                    $corBarra = 'bg-emerald-500';
                    $textoStatus = 'Vagas Livres';
                }
            } else {
                $percentual = 0;
                $nivel = 'sem_escala';
                $corBadge = 'bg-slate-100 text-slate-500 border-slate-200';
                $corBarra = 'bg-slate-300';
                $textoStatus = 'Sem Escala';
            }

            $diasGrade[] = [
                'data' => $dataStr,
                'dia' => $cursor->day,
                'dia_semana' => $diaSemanaIso,
                'nome_dia' => $cursor->translatedFormat('D'),
                'is_mes_atual' => $isMesAtual,
                'is_hoje' => $cursor->isToday(),
                'is_passado' => $cursor->isPast() && ! $cursor->isToday(),
                'capacidade' => $capacidadeDia,
                'total_agendados' => $totalAgendados,
                'total_encaixes' => $agendamentosDia->where('tipo', 'encaixe')->count(),
                'percentual_ocupacao' => $percentual,
                'nivel' => $nivel,
                'cor_badge' => $corBadge,
                'cor_barra' => $corBarra,
                'texto_status' => $textoStatus,
                'turnos' => [
                    'manha' => $agendamentosDia->where('turno', 'manha')->count(),
                    'tarde' => $agendamentosDia->where('turno', 'tarde')->count(),
                    'noite' => $agendamentosDia->where('turno', 'noite')->count(),
                ],
                'concluidos' => $concluidosDia,
                'faltas' => $faltasDia,
            ];

            $cursor->addDay();
        }

        $taxaOcupacaoGeral = $capacidadeTotalMes > 0
            ? round(($totalAgendadosMes / $capacidadeTotalMes) * 100, 1)
            : 0.0;

        $totalRealizadosOuFaltas = $totalConcluidos + $totalFaltas;
        $taxaAbsenteismo = $totalRealizadosOuFaltas > 0
            ? round(($totalFaltas / $totalRealizadosOuFaltas) * 100, 1)
            : 0.0;

        return [
            'ano' => $ano,
            'mes' => $mes,
            'nome_mes' => ucfirst($dataInicioMes->translatedFormat('F')),
            'mes_ano_texto' => ucfirst($dataInicioMes->translatedFormat('F \\d\\e Y')),
            'data_anterior' => $dataInicioMes->copy()->subMonth(),
            'data_proxima' => $dataInicioMes->copy()->addMonth(),
            'dias_grade' => $diasGrade,
            'kpis' => [
                'capacidade_total' => $capacidadeTotalMes,
                'total_agendados' => $totalAgendadosMes,
                'taxa_ocupacao' => $taxaOcupacaoGeral,
                'dias_lotados' => $diasLotadosCount,
                'total_concluidos' => $totalConcluidos,
                'total_faltas' => $totalFaltas,
                'taxa_absenteismo' => $taxaAbsenteismo,
            ],
        ];
    }
}
