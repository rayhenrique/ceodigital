<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AgendamentoFormRequest;
use App\Models\Agendamento;
use App\Models\Dentista;
use App\Models\DentistaGrade;
use App\Models\Especialidade;
use App\Models\Paciente;
use App\Services\AgendamentoService;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AgendaController extends Controller
{
    public function __construct(
        protected AgendamentoService $agendamentoService
    ) {}

    /**
     * Tela operacional da Agenda diária com visualização por turnos (Manhã, Tarde, Noite).
     */
    public function index(Request $request): View
    {
        $dataStr = $request->input('data', now()->toDateString());
        $dataCarbon = Carbon::parse($dataStr);
        $diaSemana = $dataCarbon->dayOfWeekIso; // 1 (Seg) a 7 (Dom)

        $turnoAtivo = $request->input('turno', 'manha');
        $dentistaId = $request->input('dentista_id');

        // Dentistas que possuem escala no dia da semana selecionado
        $dentistasEscalados = Dentista::query()
            ->with(['especialidade', 'grades' => fn ($q) => $q->where('dia_semana', $diaSemana)])
            ->whereHas('grades', fn ($q) => $q->where('dia_semana', $diaSemana))
            ->where('status_ativo', true)
            ->orderBy('nome_completo')
            ->get();

        // Agendamentos filtrados com Eager Loading estrito
        $agendamentos = Agendamento::query()
            ->with(['paciente.ubs', 'dentista', 'especialidade'])
            ->whereDate('data_agendamento', $dataStr)
            ->where('turno', $turnoAtivo)
            ->when($dentistaId, fn ($q) => $q->where('dentista_id', (int) $dentistaId))
            ->orderByRaw("CASE status WHEN 'em_atendimento' THEN 1 WHEN 'presente' THEN 2 WHEN 'agendado' THEN 3 WHEN 'concluido' THEN 4 WHEN 'falta' THEN 5 WHEN 'cancelado' THEN 6 ELSE 7 END")
            ->orderBy('horario_chegada', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Totais por turno no dia para os badges das abas
        $contagemTurnos = [
            'manha' => Agendamento::whereDate('data_agendamento', $dataStr)->where('turno', 'manha')->whereNotIn('status', ['cancelado'])->count(),
            'tarde' => Agendamento::whereDate('data_agendamento', $dataStr)->where('turno', 'tarde')->whereNotIn('status', ['cancelado'])->count(),
            'noite' => Agendamento::whereDate('data_agendamento', $dataStr)->where('turno', 'noite')->whereNotIn('status', ['cancelado'])->count(),
        ];

        return view('agenda.index', compact(
            'dataStr',
            'dataCarbon',
            'diaSemana',
            'turnoAtivo',
            'dentistaId',
            'dentistasEscalados',
            'agendamentos',
            'contagemTurnos'
        ));
    }

    /**
     * Formulário de novo agendamento.
     */
    public function create(Request $request): View
    {
        $pacienteId = $request->input('paciente_id') ?? old('paciente_id');
        $pacientePreSelecionado = $pacienteId ? Paciente::with('ubs')->find($pacienteId) : null;

        $especialidades = Especialidade::ativas()->orderBy('nome')->get();
        $dentistas = Dentista::with(['especialidade', 'grades'])->where('status_ativo', true)->orderBy('nome_completo')->get();

        return view('agenda.create', compact('pacientePreSelecionado', 'especialidades', 'dentistas'));
    }

    /**
     * Persiste o agendamento através do AgendamentoService.
     */
    public function store(AgendamentoFormRequest $request): RedirectResponse
    {
        try {
            $userId = (int) Auth::id();
            $dados = $request->validated();

            if ($dados['tipo'] === 'encaixe') {
                $agendamento = $this->agendamentoService->realizarEncaixe($dados, $userId);
            } else {
                $agendamento = $this->agendamentoService->agendar($dados, $userId);
            }

            return redirect()
                ->route('agenda.index', [
                    'data' => Carbon::parse($agendamento->data_agendamento)->toDateString(),
                    'turno' => $agendamento->turno,
                ])
                ->with('success', 'Consulta agendada com sucesso.');
        } catch (DomainException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao registrar agendamento: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ocorreu um erro ao processar o agendamento: ' . $e->getMessage());
        }
    }

    /**
     * Registra a chegada do paciente na recepção do CEO.
     */
    public function registrarChegada(Agendamento $agendamento): RedirectResponse
    {
        try {
            $this->agendamentoService->atualizarStatusChegada(
                agendamentoId: $agendamento->id,
                status: 'presente',
                horarioChegada: now()->format('H:i:s')
            );

            return back()->with('success', 'Chegada do paciente registrada com sucesso!');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Atualização rápida do status do atendimento (chegada, em atendimento, concluído, falta, cancelado).
     */
    public function atualizarStatus(Request $request, Agendamento $agendamento): RedirectResponse|JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:agendado,presente,em_atendimento,concluido,falta,cancelado'],
            'horario_chegada' => ['nullable', 'date_format:H:i:s,H:i'],
        ]);

        try {
            $atualizado = $this->agendamentoService->atualizarStatusChegada(
                agendamentoId: $agendamento->id,
                status: $request->input('status'),
                horarioChegada: $request->input('horario_chegada')
            );

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'status' => $atualizado->status,
                    'horario_chegada' => $atualizado->horario_chegada,
                ]);
            }

            return back()->with('success', 'Status do atendimento atualizado.');
        } catch (DomainException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancelamento ou exclusão de agendamento.
     */
    public function destroy(Agendamento $agendamento): RedirectResponse
    {
        $this->agendamentoService->atualizarStatusChegada($agendamento->id, 'cancelado');

        return back()->with('success', 'Agendamento cancelado com sucesso.');
    }
}
