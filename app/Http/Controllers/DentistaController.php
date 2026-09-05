<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\DentistaFormRequest;
use App\Models\Dentista;
use App\Models\DentistaGrade;
use App\Models\Especialidade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DentistaController extends Controller
{
    /**
     * Listagem de profissionais com especialidade e grades de atendimento.
     */
    public function index(Request $request): View
    {
        $busca = $request->input('busca');

        $dentistas = Dentista::query()
            ->with(['especialidade', 'grades'])
            ->withCount('agendamentos')
            ->when($busca, function ($query, $busca) {
                $query->where('nome_completo', 'like', "%{$busca}%")
                      ->orWhere('cro', 'like', "%{$busca}%");
            })
            ->orderBy('nome_completo')
            ->paginate(12)
            ->withQueryString();

        return view('dentistas.index', compact('dentistas', 'busca'));
    }

    /**
     * Formulário de cadastro de dentista (Admin).
     */
    public function create(): View
    {
        $especialidades = Especialidade::ativas()->orderBy('nome')->get();

        return view('dentistas.create', compact('especialidades'));
    }

    /**
     * Persiste o dentista e suas grades de turno em transação atômica.
     */
    public function store(DentistaFormRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $dados = $request->safe()->except('grades');
            $dados['status_ativo'] = $request->boolean('status_ativo', true);

            $dentista = Dentista::create($dados);

            if ($request->has('grades') && is_array($request->grades)) {
                foreach ($request->grades as $gradeData) {
                    if (! empty($gradeData['dia_semana']) && ! empty($gradeData['turno'])) {
                        DentistaGrade::create([
                            'dentista_id' => $dentista->id,
                            'dia_semana' => (int) $gradeData['dia_semana'],
                            'turno' => $gradeData['turno'],
                            'vagas_padrao' => (int) ($gradeData['vagas_padrao'] ?? 8),
                        ]);
                    }
                }
            }
        });

        return redirect()
            ->route('dentistas.index')
            ->with('success', 'Profissional e escala de turnos cadastrados com sucesso.');
    }

    /**
     * Exibe o perfil, especialidade e grade operacional do dentista.
     */
    public function show(Dentista $dentista): View
    {
        $dentista->load([
            'especialidade',
            'grades' => fn ($q) => $q->orderBy('dia_semana')->orderBy('turno'),
            'agendamentos' => fn ($q) => $q->with('paciente')->latest('data_agendamento')->limit(15),
        ]);

        return view('dentistas.show', compact('dentista'));
    }

    /**
     * Formulário de edição de dados e escala do dentista (Admin).
     */
    public function edit(Dentista $dentista): View
    {
        $dentista->load('grades');
        $especialidades = Especialidade::ativas()->orderBy('nome')->get();

        return view('dentistas.edit', compact('dentista', 'especialidades'));
    }

    /**
     * Atualiza dados e sincroniza as grades operacionais.
     */
    public function update(DentistaFormRequest $request, Dentista $dentista): RedirectResponse
    {
        DB::transaction(function () use ($request, $dentista) {
            $dados = $request->safe()->except('grades');
            $dados['status_ativo'] = $request->boolean('status_ativo');

            $dentista->update($dados);

            // Sincroniza grades operacionais
            if ($request->has('grades') && is_array($request->grades)) {
                $dentista->grades()->delete();

                foreach ($request->grades as $gradeData) {
                    if (! empty($gradeData['dia_semana']) && ! empty($gradeData['turno'])) {
                        DentistaGrade::create([
                            'dentista_id' => $dentista->id,
                            'dia_semana' => (int) $gradeData['dia_semana'],
                            'turno' => $gradeData['turno'],
                            'vagas_padrao' => (int) ($gradeData['vagas_padrao'] ?? 8),
                        ]);
                    }
                }
            }
        });

        return redirect()
            ->route('dentistas.index')
            ->with('success', 'Dados e escala do profissional atualizados com sucesso.');
    }

    /**
     * Exclui o dentista caso não possua agendamentos cadastrados.
     */
    public function destroy(Dentista $dentista): RedirectResponse
    {
        if ($dentista->agendamentos()->exists()) {
            return redirect()
                ->route('dentistas.index')
                ->with('error', 'Não é possível excluir o dentista pois existem agendamentos vinculados a este profissional.');
        }

        $dentista->delete();

        return redirect()
            ->route('dentistas.index')
            ->with('success', 'Profissional removido com sucesso.');
    }
}
