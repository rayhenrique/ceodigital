<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ExpurgarAuditoriaRequest;
use App\Models\Auditoria;
use App\Models\User;
use App\Services\AuditoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditoriaController extends Controller
{
    public function __construct(
        protected AuditoriaService $auditoriaService
    ) {}

    /**
     * Trilha de auditoria detalhada de ações do sistema (RF24 - Exclusivo Admin).
     */
    public function index(Request $request): View
    {
        $filtros = $request->only(['tabela_afetada', 'acao', 'user_id', 'data_inicio', 'data_fim']);

        $auditorias = $this->auditoriaService
            ->buildQuery($filtros)
            ->paginate(20)
            ->withQueryString();

        $tabelasDisponiveis = Auditoria::select('tabela_afetada')->distinct()->pluck('tabela_afetada');
        $usuariosAuditados = User::orderBy('name')->get(['id', 'name']);

        return view('auditorias.index', [
            'auditorias' => $auditorias,
            'tabelasDisponiveis' => $tabelasDisponiveis,
            'usuariosAuditados' => $usuariosAuditados,
            'tabela' => $filtros['tabela_afetada'] ?? null,
            'acao' => $filtros['acao'] ?? null,
            'userId' => $filtros['user_id'] ?? null,
            'dataInicio' => $filtros['data_inicio'] ?? null,
            'dataFim' => $filtros['data_fim'] ?? null,
            'diasRetencaoPadrao' => config('audit.retention_days', 180),
        ]);
    }

    /**
     * Exporta os registros de auditoria filtrados em formato CSV via streaming.
     */
    public function exportar(Request $request): StreamedResponse
    {
        $filtros = $request->only(['tabela_afetada', 'acao', 'user_id', 'data_inicio', 'data_fim']);

        return $this->auditoriaService->streamCsv($filtros);
    }

    /**
     * Expurga registros antigos de auditoria mantendo apenas os dias selecionados pelo administrador.
     */
    public function expurgar(ExpurgarAuditoriaRequest $request): RedirectResponse
    {
        $dias = (int) $request->validated('dias');

        $removidos = $this->auditoriaService->expurgar($dias);

        if ($removidos === 0) {
            return back()->with('info', "Nenhum registro de auditoria com mais de {$dias} dias foi encontrado para expurgo.");
        }

        return back()->with('success', "Sucesso: {$removidos} registro(s) de auditoria com mais de {$dias} dias foram expurgados definitivamente.");
    }

    /**
     * Visualização detalhada do snapshot anterior vs novo de uma ação auditada.
     */
    public function show(Auditoria $auditoria): View
    {
        $auditoria->load('user');

        return view('auditorias.show', compact('auditoria'));
    }
}
