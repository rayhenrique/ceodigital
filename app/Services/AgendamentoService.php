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
}
