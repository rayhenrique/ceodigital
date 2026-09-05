<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PacienteFormRequest;
use App\Models\Paciente;
use App\Models\Ubs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PacienteController extends Controller
{
    /**
     * Listagem de pacientes com busca rápida unificada por Nome, CPF ou CNS.
     * Suporta resposta JSON para buscas reativas (autocomplete / Alpine.js).
     */
    public function index(Request $request): View|JsonResponse
    {
        $busca = $request->input('busca');
        $buscaLimpa = $busca ? preg_replace('/\D/', '', (string) $busca) : '';

        $query = Paciente::query()
            ->with('ubs')
            ->when($busca, function ($q) use ($busca, $buscaLimpa) {
                $q->where(function ($sub) use ($busca, $buscaLimpa) {
                    $sub->where('nome_completo', 'like', "%{$busca}%");

                    if (! empty($buscaLimpa)) {
                        $sub->orWhere('cpf', 'like', "%{$buscaLimpa}%")
                            ->orWhere('cns', 'like', "%{$buscaLimpa}%");
                    }
                });
            })
            ->orderBy('nome_completo');

        // Retorno JSON para busca reativa com Alpine.js
        if ($request->wantsJson() || $request->ajax()) {
            $pacientes = $query->limit(10)->get()->map(fn ($p) => [
                'id' => $p->id,
                'nome' => $p->nome_completo,
                'cpf' => $p->cpf,
                'cns' => $p->cns,
                'ubs' => $p->ubs?->nome ?? 'N/A',
                'telefone' => $p->telefone_1,
            ]);

            return response()->json($pacientes);
        }

        $pacientes = $query->paginate(15)->withQueryString();

        return view('pacientes.index', compact('pacientes', 'busca'));
    }

    /**
     * Formulário de cadastro de paciente.
     */
    public function create(): View
    {
        $ubsList = Ubs::orderBy('nome')->get();

        return view('pacientes.create', compact('ubsList'));
    }

    /**
     * Armazena o paciente validado.
     */
    public function store(PacienteFormRequest $request): RedirectResponse
    {
        $paciente = Paciente::create($request->validated());

        return redirect()
            ->route('pacientes.show', $paciente)
            ->with('success', 'Paciente cadastrado com sucesso no CEO Digital.');
    }

    /**
     * Prontuário e histórico de atendimentos do paciente.
     */
    public function show(Paciente $paciente): View
    {
        $paciente->load([
            'ubs',
            'agendamentos' => function ($q) {
                $q->with(['dentista', 'especialidade'])->orderBy('data_agendamento', 'desc');
            },
            'demandasReprimidas' => function ($q) {
                $q->with('especialidade')->orderBy('data_solicitacao', 'desc');
            },
        ]);

        return view('pacientes.show', compact('paciente'));
    }

    /**
     * Formulário de edição de dados do paciente.
     */
    public function edit(Paciente $paciente): View
    {
        $ubsList = Ubs::orderBy('nome')->get();

        return view('pacientes.edit', compact('paciente', 'ubsList'));
    }

    /**
     * Atualiza os dados do paciente.
     */
    public function update(PacienteFormRequest $request, Paciente $paciente): RedirectResponse
    {
        $paciente->update($request->validated());

        return redirect()
            ->route('pacientes.show', $paciente)
            ->with('success', 'Dados do paciente atualizados com sucesso.');
    }

    /**
     * Exclui o paciente caso não haja agendamentos cadastrados.
     */
    public function destroy(Paciente $paciente): RedirectResponse
    {
        if ($paciente->agendamentos()->exists()) {
            return redirect()
                ->route('pacientes.index')
                ->with('error', 'Não é possível remover o paciente pois ele possui histórico de agendamentos no sistema.');
        }

        $paciente->delete();

        return redirect()
            ->route('pacientes.index')
            ->with('success', 'Paciente excluído com sucesso.');
    }
}
