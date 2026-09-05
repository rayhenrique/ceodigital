<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UbsFormRequest;
use App\Models\Ubs;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UbsController extends Controller
{
    /**
     * Listagem paginada de UBSs com filtro de busca.
     */
    public function index(Request $request): View
    {
        $busca = $request->input('busca');

        $unidades = Ubs::query()
            ->withCount('pacientes')
            ->when($busca, function ($query, $busca) {
                $query->where('nome', 'like', "%{$busca}%")
                      ->orWhere('diretor', 'like', "%{$busca}%")
                      ->orWhere('endereco', 'like', "%{$busca}%");
            })
            ->orderBy('nome')
            ->paginate(10)
            ->withQueryString();

        return view('ubs.index', compact('unidades', 'busca'));
    }

    /**
     * Formulário de cadastro de nova UBS.
     */
    public function create(): View
    {
        return view('ubs.create');
    }

    /**
     * Persiste uma nova UBS no banco de dados.
     */
    public function store(UbsFormRequest $request): RedirectResponse
    {
        $ubs = Ubs::create($request->validated());

        return redirect()
            ->route('ubs.index')
            ->with('success', "Unidade Básica de Saúde '{$ubs->nome}' cadastrada com sucesso.");
    }

    /**
     * Detalhes e pacientes vinculados à UBS.
     */
    public function show(Ubs $ub): View
    {
        $ub->load('pacientes');
        $ub->loadCount('pacientes');

        return view('ubs.show', ['ubs' => $ub]);
    }

    /**
     * Formulário de edição da UBS.
     */
    public function edit(Ubs $ub): View
    {
        $ub->loadCount('pacientes');

        return view('ubs.edit', ['ubs' => $ub]);
    }

    /**
     * Atualiza os dados da UBS.
     */
    public function update(UbsFormRequest $request, Ubs $ub): RedirectResponse
    {
        $ub->update($request->validated());

        return redirect()
            ->route('ubs.index')
            ->with('success', "Dados da UBS '{$ub->nome}' atualizados com sucesso.");
    }

    /**
     * Remove a UBS se não houver pacientes associados.
     */
    public function destroy(Ubs $ub): RedirectResponse
    {
        if ($ub->pacientes()->exists()) {
            $total = $ub->pacientes()->count();
            return redirect()
                ->route('ubs.index')
                ->with('error', "Não é possível excluir a UBS '{$ub->nome}' pois existem {$total} paciente(s) vinculados a ela.");
        }

        $nome = $ub->nome;
        $ub->delete();

        return redirect()
            ->route('ubs.index')
            ->with('success', "Unidade Básica de Saúde '{$nome}' excluída com sucesso.");
    }
}
