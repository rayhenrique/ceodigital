<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\DemandaReprimidaFormRequest;
use App\Models\DemandaReprimida;
use App\Models\Dentista;
use App\Models\Especialidade;
use App\Models\Paciente;
use App\Services\AgendamentoService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DemandaReprimidaController extends Controller
{
    public function __construct(
        protected AgendamentoService $agendamentoService
    ) {}

    /**
     * Listagem da fila de espera / demanda reprimida com filtros de prioridade e especialidade.
     */
    public function index(Request $request): View
    {
        $especialidadeId = $request->input('especialidade_id');
        $prioridade = $request->input('prioridade');
        $status = $request->input('status', 'aguardando');

        $demandas = DemandaReprimida::query()
            ->with(['paciente.ubs', 'especialidade'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($especialidadeId, fn ($q) => $q->where('especialidade_id', (int) $especialidadeId))
            ->when($prioridade, fn ($q) => $q->where('prioridade', $prioridade))
            ->orderByRaw("CASE WHEN prioridade = 'urgente' THEN 0 ELSE 1 END")
            ->orderBy('data_solicitacao', 'asc')
            ->paginate(15)
            ->withQueryString();

        $especialidades = Especialidade::ativas()->orderBy('nome')->get();
        $dentistas = Dentista::with('grades')->where('status_ativo', true)->orderBy('nome_completo')->get();

        return view('demanda-reprimida.index', compact(
            'demandas',
            'especialidades',
            'dentistas',
            'especialidadeId',
            'prioridade',
            'status'
        ));
    }

    /**
     * Formulário de inserção na lista de espera.
     */
    public function create(Request $request): View
    {
        $pacienteId = $request->input('paciente_id');
        $pacientePreSelecionado = $pacienteId ? Paciente::with('ubs')->find($pacienteId) : null;
        $especialidades = Especialidade::ativas()->orderBy('nome')->get();

        return view('demanda-reprimida.create', compact('pacientePreSelecionado', 'especialidades'));
    }

    /**
     * Armazena uma nova solicitação na fila de espera.
     */
    public function store(DemandaReprimidaFormRequest $request): RedirectResponse
    {
        DemandaReprimida::create($request->validated());

        return redirect()
            ->route('demanda-reprimida.index')
            ->with('success', 'Paciente inserido na fila de espera da especialidade com sucesso.');
    }

    /**
     * Converte e promove o registro da fila de espera diretamente em agendamento (RF20).
     */
    public function promover(Request $request, DemandaReprimida $demanda): RedirectResponse
    {
        $request->validate([
            'dentista_id' => ['required', 'integer', 'exists:dentistas,id'],
            'data_agendamento' => ['required', 'date', 'after_or_equal:today'],
            'turno' => ['required', 'in:manha,tarde,noite'],
            'tipo' => ['nullable', 'in:normal,encaixe'],
        ]);

        try {
            $agendamento = $this->agendamentoService->promoverDemandaReprimida(
                demandaReprimidaId: $demanda->id,
                dadosAgendamento: [
                    'dentista_id' => (int) $request->input('dentista_id'),
                    'data_agendamento' => $request->input('data_agendamento'),
                    'turno' => $request->input('turno'),
                    'tipo' => $request->input('tipo', 'normal'),
                ],
                userId: (int) Auth::id()
            );

            return redirect()
                ->route('agenda.index', [
                    'data' => $agendamento->data_agendamento->toDateString(),
                    'turno' => $agendamento->turno,
                ])
                ->with('success', "Paciente {$demanda->paciente->nome_completo} promovido da fila com sucesso para a agenda!");
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Marca o paciente como desistente na fila.
     */
    public function destroy(DemandaReprimida $demanda): RedirectResponse
    {
        $demanda->marcarComoDesistente();

        return redirect()
            ->route('demanda-reprimida.index')
            ->with('success', 'Registro da fila de espera marcado como desistente.');
    }
}
