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
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                    <svg class="w-3.5 h-3.5 mr-1 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Retenção Padrão: <strong class="ml-1 text-slate-900">{{ $diasRetencaoPadrao }} dias</strong>
                </span>

                <a href="{{ route('auditorias.exportar', request()->query()) }}" class="inline-flex items-center px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold tracking-wide transition shadow-xs">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Exportar CSV
                </a>

                <button type="button" @click="$dispatch('abrir-modal-expurgo')" class="inline-flex items-center px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold tracking-wide transition shadow-xs">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Expurgar Histórico
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        expurgoModalOpen: false,
        diasExpurgo: 180,
        confirmacaoTexto: ''
    }" @abrir-modal-expurgo.window="expurgoModalOpen = true; confirmacaoTexto = '';">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Mensagens de Feedback -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg text-sm flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('info'))
                <div class="p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-800 rounded-r-lg text-sm flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('info') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-lg text-sm shadow-xs space-y-1">
                    <div class="font-bold flex items-center gap-1.5">
                        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Atenção ao executar a operação:
                    </div>
                    <ul class="list-disc list-inside pl-1 text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between text-xs text-slate-500 gap-2 bg-slate-50/50">
                    <div>
                        Exibindo <span class="font-bold text-slate-800">{{ $auditorias->firstItem() ?? 0 }}</span> a <span class="font-bold text-slate-800">{{ $auditorias->lastItem() ?? 0 }}</span> de <span class="font-bold text-slate-800">{{ $auditorias->total() }}</span> registros
                    </div>
                    @if(request()->anyFilled(['tabela_afetada', 'acao', 'user_id', 'data_inicio', 'data_fim']))
                        <div class="inline-flex items-center gap-1.5 text-amber-700 bg-amber-50 px-2.5 py-1 rounded-md border border-amber-200 font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Filtros aplicados. Os dados exportados para CSV respeitarão esta mesma filtragem.
                        </div>
                    @endif
                </div>

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
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold {{ str_contains(strtoupper((string) $audit->acao), 'CRIAR') ? 'bg-emerald-100 text-emerald-800' : (str_contains(strtoupper((string) $audit->acao), 'REMOVER') ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800') }}">
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

        <!-- Modal de Expurgar Histórico -->
        <div x-show="expurgoModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="expurgoModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200"
                     @click.away="expurgoModalOpen = false">
                    
                    <form method="POST" action="{{ route('auditorias.expurgar') }}">
                        @csrf
                        <div class="bg-rose-50 px-6 py-4 border-b border-rose-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center text-rose-600 shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-rose-950">Expurgar Histórico Antigo</h3>
                                    <p class="text-xs text-rose-700">Libere espaço no banco de dados com segurança</p>
                                </div>
                            </div>
                            <button type="button" @click="expurgoModalOpen = false" class="text-rose-400 hover:text-rose-600 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-4">
                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-900 leading-relaxed">
                                <div class="font-bold flex items-center gap-1.5 mb-1 text-amber-950">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Como funciona a retenção?
                                </div>
                                O sistema já executa um expurgo automático diário às 03:00 para logs com mais de {{ $diasRetencaoPadrao }} dias. Você pode usar esta ferramenta para fazer uma limpeza antecipada imediata.
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">
                                    Manter apenas os últimos:
                                </label>
                                <select name="dias" x-model="diasExpurgo" class="w-full py-2 border-slate-300 rounded-lg text-sm focus:ring-rose-500 focus:border-rose-500">
                                    <option value="365">365 dias (1 ano de histórico)</option>
                                    <option value="180">180 dias (6 meses de histórico - Recomendado)</option>
                                    <option value="90">90 dias (3 meses de histórico)</option>
                                    <option value="60">60 dias (2 meses de histórico)</option>
                                    <option value="30">30 dias (1 mês de histórico)</option>
                                </select>
                                <p class="text-[11px] text-slate-500 mt-1">
                                    Todos os registros de auditoria com data anterior a esse período serão permanentemente deletados.
                                </p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">
                                    Confirmação de Segurança
                                </label>
                                <p class="text-xs text-slate-600 mb-2">
                                    Para confirmar a exclusão irreversível, digite a palavra <strong class="text-rose-700 font-mono">EXPURGAR</strong> no campo abaixo:
                                </p>
                                <input type="text" 
                                       name="confirmacao" 
                                       x-model="confirmacaoTexto" 
                                       placeholder="Digite EXPURGAR" 
                                       autocomplete="off"
                                       class="w-full py-2 font-mono text-sm border-slate-300 rounded-lg focus:ring-rose-500 focus:border-rose-500 uppercase tracking-widest">
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-3 border-t border-slate-200 flex justify-end gap-2">
                            <button type="button" 
                                    @click="expurgoModalOpen = false" 
                                    class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                                Cancelar
                            </button>
                            <button type="submit" 
                                    :disabled="confirmacaoTexto !== 'EXPURGAR'"
                                    :class="confirmacaoTexto === 'EXPURGAR' ? 'bg-rose-600 hover:bg-rose-700 text-white cursor-pointer shadow-xs' : 'bg-slate-300 text-slate-500 cursor-not-allowed'"
                                    class="px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Confirmar Expurgo</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
