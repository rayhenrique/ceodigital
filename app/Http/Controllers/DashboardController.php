<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Services\RelatorioService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected RelatorioService $relatorioService
    ) {}

    /**
     * Exibe o painel operacional com indicadores do dia, turno corrente e atendimentos.
     */
    public function __invoke(): View
    {
        $metricas = $this->relatorioService->obterMetricasDashboard();

        // Atendimentos do dia com Eager Loading para prevenir N+1
        $atendimentosHoje = Agendamento::query()
            ->with(['paciente.ubs', 'dentista', 'especialidade'])
            ->doDia()
            ->orderByRaw("CASE status WHEN 'em_atendimento' THEN 1 WHEN 'presente' THEN 2 WHEN 'agendado' THEN 3 WHEN 'concluido' THEN 4 WHEN 'falta' THEN 5 WHEN 'cancelado' THEN 6 ELSE 7 END")
            ->orderBy('horario_chegada', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Determina o turno corrente com base no horário
        $horaAtual = (int) now()->format('H');
        $turnoCorrente = match (true) {
            $horaAtual >= 6 && $horaAtual < 13 => 'manha',
            $horaAtual >= 13 && $horaAtual < 18 => 'tarde',
            default => 'noite',
        };

        return view('dashboard', compact('metricas', 'atendimentosHoje', 'turnoCorrente'));
    }
}
