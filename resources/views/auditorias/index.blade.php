<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Log de Auditoria do Sistema
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Trilha imutável de eventos, mutações de dados e segurança operacional
                </p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200">
                Acesso Restrito: Administrador
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Card -->
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                <form method="GET" action="{{ route('auditorias.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Tabela -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Entidade / Tabela</label>
                        <select name="tabela_afetada" class="w-full py-1.5 border-slate-300 rounded text-xs focus:ring-amber-500">
                            <option value="">Todas as Tabelas</option>
                            @foreach($tabelasDisponiveis as $tab)
                                <option value="{{ $tab }}" {{ ($tabela ?? '') === $tab ? 'selected' : '' }}>{{ $tab }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Ação -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Ação Executada</label>
                        <input type="text" name="acao" value="{{ $acao ?? '' }}" placeholder="Ex: CRIAR, ATUALIZAR, REMOVER" class="w-full py-1.5 border-slate-300 rounded text-xs focus:ring-amber-500">
                    </div>

                    <!-- Usuário -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Usuário Operador</label>
                        <select name="user_id" class="w-full py-1.5 border-slate-300 rounded text-xs focus:ring-amber-500">
                            <option value="">Todos os Usuários</option>
                            @foreach($usuariosAuditados as $u)
                                <option value="{{ $u->id }}" {{ ($userId ?? '') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Data Início -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Data Início</label>
                        <input type="date" name="data_inicio" value="{{ $dataInicio ?? '' }}" class="w-full py-1.5 border-slate-300 rounded text-xs focus:ring-amber-500">
                    </div>

                    <!-- Botão Filtrar -->
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 py-1.5 bg-slate-800 text-white rounded text-xs font-bold hover:bg-slate-900 transition">
                            Filtrar
                        </button>
                        <a href="{{ route('auditorias.index') }}" class="px-3 py-1.5 bg-slate-100 text-slate-600 rounded text-xs font-bold hover:bg-slate-200 transition">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 min-w-[700px]">
                        <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3.5">Data / Hora</th>
                                <th class="px-4 py-3.5">Operador</th>
                                <th class="px-4 py-3.5">Ação</th>
                                <th class="px-4 py-3.5">Entidade / Registro</th>
                                <th class="px-4 py-3.5">IP de Origem</th>
                                <th class="px-4 py-3.5 text-right">Detalhes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($auditorias as $audit)
                                <tr class="hover:bg-slate-50 transition font-mono text-xs">
                                    <td class="px-4 py-3 whitespace-nowrap text-slate-500">
                                        {{ $audit->created_at->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap font-sans font-medium text-slate-900">
                                        {{ $audit->user->name ?? 'Sistema / Cron' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold {{ str_contains(strtoupper($audit->acao), 'CRIAR') ? 'bg-emerald-100 text-emerald-800' : (str_contains(strtoupper($audit->acao), 'REMOVER') ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800') }}">
                                            {{ $audit->acao }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap font-sans">
                                        <span class="font-bold text-slate-800">{{ $audit->tabela_afetada }}</span>
                                        <span class="text-slate-400 font-mono">#{{ $audit->registro_id }}</span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-slate-400">
                                        {{ $audit->ip_address ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap font-sans">
                                        <a href="{{ route('auditorias.show', $audit) }}" class="px-2.5 py-1 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded text-xs font-semibold inline-flex items-center gap-1 transition">
                                            <span>Inspecionar</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400 font-sans text-sm">
                                        Nenhum registro de auditoria encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($auditorias->hasPages())
                    <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                        {{ $auditorias->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
