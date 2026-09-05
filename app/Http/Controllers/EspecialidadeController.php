<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\EspecialidadeFormRequest;
use App\Models\Especialidade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EspecialidadeController extends Controller
{
    /**
     * Listagem de especialidades odontológicas.
     */
    public function index(Request $request): View
    {
        $busca = $request->input('busca');

        $especialidades = Especialidade::query()
            ->withCount(['dentistas', 'agendamentos', 'demandasReprimidas'])
            ->when($busca, function ($query, $busca) {
                $query->where('nome', 'like', "%{$busca}%");
            })
            ->orderBy('nome')
            ->paginate(10)
            ->withQueryString();

        return view('especialidades.index', compact('especialidades', 'busca'));
    }

    /**
     * Formulário de cadastro de especialidade (Admin).
     */
    public function create(): View
    {
        return view('especialidades.create');
    }

    /**
     * Salva nova especialidade.
     */
    public function store(EspecialidadeFormRequest $request): RedirectResponse
    {
        $dados = $request->validated();
        $dados['status_ativo'] = $request->boolean('status_ativo', true);

        $esp = Especialidade::create($dados);

        return redirect()
            ->route('especialidades.index')
            ->with('success', "Especialidade '{$esp->nome}' cadastrada com sucesso.");
    }

    /**
     * Formulário de edição de especialidade (Admin).
     */
    public function edit(Especialidade $especialidade): View
    {
        $especialidade->loadCount(['dentistas', 'agendamentos', 'demandasReprimidas']);

        return view('especialidades.edit', compact('especialidade'));
    }

    /**
     * Atualiza os dados da especialidade.
     */
    public function update(EspecialidadeFormRequest $request, Especialidade $especialidade): RedirectResponse
    {
        $dados = $request->validated();
        $dados['status_ativo'] = $request->boolean('status_ativo');

        $especialidade->update($dados);

        return redirect()
            ->route('especialidades.index')
            ->with('success', "Especialidade '{$especialidade->nome}' atualizada com sucesso.");
    }

    /**
     * Exclui especialidade se não tiver pacientes vinculados (agendamentos ou fila) nem profissionais.
     */
    public function destroy(Especialidade $especialidade): RedirectResponse
    {
        $totalAgendados = $especialidade->agendamentos()->count();
        $totalFila = $especialidade->demandasReprimidas()->count();
        $totalDentistas = $especialidade->dentistas()->count();

        if ($totalAgendados > 0 || $totalFila > 0) {
            $detalhes = [];
            if ($totalAgendados > 0) {
                $detalhes[] = "{$totalAgendados} agendamento(s) de consulta";
            }
            if ($totalFila > 0) {
                $detalhes[] = "{$totalFila} paciente(s) na fila de espera";
            }

            return redirect()
                ->route('especialidades.index')
                ->with('error', "Não é possível excluir a especialidade '{$especialidade->nome}' pois existem pacientes vinculados a ela (" . implode(' e ', $detalhes) . ").");
        }

        if ($totalDentistas > 0) {
            return redirect()
                ->route('especialidades.index')
                ->with('error', "Não é possível excluir a especialidade '{$especialidade->nome}' pois há {$totalDentistas} cirurgião(ões)-dentista(s) vinculado(s) a ela. Realoque os profissionais antes de excluir.");
        }

        $nome = $especialidade->nome;
        $especialidade->delete();

        return redirect()
            ->route('especialidades.index')
            ->with('success', "Especialidade '{$nome}' excluída com sucesso.");
    }
}
