<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Dentista;
use App\Models\Especialidade;
use App\Models\Ubs;
use App\Services\RelatorioService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RelatorioController extends Controller
{
    public function __construct(
        protected RelatorioService $relatorioService
    ) {}

    /**
     * Painel central de seleção e geração de relatórios gerenciais.
     */
    public function index(): View
    {
        return view('relatorios.index');
    }

    /**
     * Relatório de Absenteísmo e Faltas (RF22) com filtros e modo de impressão.
     */
    public function absenteismo(Request $request): View
    {
        $filtros = $request->only(['data_inicio', 'data_fim', 'especialidade_id', 'ubs_id']);
        $dados = $this->relatorioService->obterRelatorioAbsenteismo($filtros);

        $especialidades = Especialidade::ativas()->orderBy('nome')->get();
        $ubsList = Ubs::orderBy('nome')->get();
        $isPrint = $request->boolean('print');

        return view('relatorios.absenteismo', compact('dados', 'especialidades', 'ubsList', 'filtros', 'isPrint'));
    }

    /**
     * Relatório de Produção Odontológica por Dentista e Especialidade.
     */
    public function producao(Request $request): View
    {
        $filtros = $request->only(['data_inicio', 'data_fim', 'dentista_id', 'especialidade_id']);
        $dados = $this->relatorioService->obterRelatorioProducao($filtros);

        $especialidades = Especialidade::ativas()->orderBy('nome')->get();
        $dentistas = Dentista::where('status_ativo', true)->orderBy('nome_completo')->get();
        $isPrint = $request->boolean('print');

        return view('relatorios.producao', compact('dados', 'especialidades', 'dentistas', 'filtros', 'isPrint'));
    }

    /**
     * Relatório de Demanda Reprimida e Tempo Médio de Espera.
     */
    public function demandaReprimida(Request $request): View
    {
        $filtros = $request->only(['especialidade_id', 'prioridade']);
        $dados = $this->relatorioService->obterRelatorioDemandaReprimida($filtros);

        $especialidades = Especialidade::ativas()->orderBy('nome')->get();
        $isPrint = $request->boolean('print');

        return view('relatorios.demanda-reprimida', compact('dados', 'especialidades', 'filtros', 'isPrint'));
    }
}
