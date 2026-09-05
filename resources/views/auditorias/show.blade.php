<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Inspeção do Registro de Auditoria #{{ $auditoria->id }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Trilha detalhada de mutação de dados
                </p>
            </div>
            <a href="{{ route('auditorias.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-50 transition shadow-xs">
                Voltar aos Logs
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Metadados do Evento -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Metadados da Operação</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-xs text-slate-400 block">Data e Hora</span>
                        <span class="font-mono font-bold text-slate-800">{{ $auditoria->created_at->format('d/m/Y H:i:s') }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Usuário Operador</span>
                        <span class="font-bold text-slate-800">{{ $auditoria->user->name ?? 'Sistema' }}</span>
                        @if($auditoria->user)
                            <span class="text-xs text-slate-400 block font-mono">{{ $auditoria->user->email }}</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Ação Executada</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-900">
                            {{ $auditoria->acao }}
                        </span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Alvo Auditado</span>
                        <span class="font-bold text-slate-800">{{ $auditoria->tabela_afetada }}</span>
                        <span class="text-xs text-slate-500 font-mono">ID: {{ $auditoria->registro_id }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-mono text-slate-600">
                    <div>
                        <span class="text-slate-400 font-sans block">Endereço IP:</span>
                        {{ $auditoria->ip_address ?? 'Não capturado' }}
                    </div>
                    <div>
                        <span class="text-slate-400 font-sans block">User Agent do Navegador:</span>
                        <span class="break-all">{{ $auditoria->user_agent ?? 'Não capturado' }}</span>
                    </div>
                </div>
            </div>

            <!-- Diff de Dados (Antes vs Depois) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Estado Anterior -->
                <div class="bg-white rounded-xl border border-rose-200 shadow-xs overflow-hidden">
                    <div class="p-3 bg-rose-50 border-b border-rose-200 font-bold text-xs text-rose-800 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        Estado Anterior (Snapshot Original)
                    </div>
                    <div class="p-4 bg-slate-900 text-rose-300 font-mono text-xs overflow-x-auto max-h-96">
                        @if(!empty($auditoria->dados_antigos))
                            <pre>{{ json_encode($auditoria->dados_antigos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @else
                            <span class="text-slate-500 italic">Nenhum dado anterior (Registro criado).</span>
                        @endif
                    </div>
                </div>

                <!-- Estado Novo -->
                <div class="bg-white rounded-xl border border-emerald-200 shadow-xs overflow-hidden">
                    <div class="p-3 bg-emerald-50 border-b border-emerald-200 font-bold text-xs text-emerald-800 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Novo Estado (Snapshot Persistido)
                    </div>
                    <div class="p-4 bg-slate-900 text-emerald-300 font-mono text-xs overflow-x-auto max-h-96">
                        @if(!empty($auditoria->dados_novos))
                            <pre>{{ json_encode($auditoria->dados_novos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @else
                            <span class="text-slate-500 italic">Nenhum dado novo (Registro excluído).</span>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
