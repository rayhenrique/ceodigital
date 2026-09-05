<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditoriaController extends Controller
{
    /**
     * Trilha de auditoria detalhada de ações do sistema (RF24 - Exclusivo Admin).
     */
    public function index(Request $request): View
    {
        $tabela = $request->input('tabela_afetada');
        $acao = $request->input('acao');
        $userId = $request->input('user_id');
        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');

        $auditorias = Auditoria::query()
            ->with('user')
            ->when($tabela, fn ($q) => $q->where('tabela_afetada', $tabela))
            ->when($acao, fn ($q) => $q->where('acao', 'like', "%{$acao}%"))
            ->when($userId, fn ($q) => $q->where('user_id', (int) $userId))
            ->when($dataInicio, fn ($q) => $q->whereDate('created_at', '>=', $dataInicio))
            ->when($dataFim, fn ($q) => $q->whereDate('created_at', '<=', $dataFim))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $tabelasDisponiveis = Auditoria::select('tabela_afetada')->distinct()->pluck('tabela_afetada');
        $usuariosAuditados = User::orderBy('name')->get(['id', 'name']);

        return view('auditorias.index', compact(
            'auditorias',
            'tabelasDisponiveis',
            'usuariosAuditados',
            'tabela',
            'acao',
            'userId',
            'dataInicio',
            'dataFim'
        ));
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
