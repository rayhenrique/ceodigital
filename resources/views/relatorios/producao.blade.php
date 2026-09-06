<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Relatório de Produção Odontológica
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Atendimentos concluídos por cirurgião-dentista e especialidade
                </p>
            </div>
            <div class="flex items-center gap-2 no-print">
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white rounded-lg font-semibold text-xs uppercase tracking-widest hover:bg-slate-900 transition shadow-xs">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Imprimir / Salvar PDF
                </button>
                <a href="{{ route('relatorios.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg font-semibold text-xs uppercase tracking-widest hover:bg-slate-50 transition shadow-xs">
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Cabeçalho Oficial SUS (Visível na impressão) -->
            <div class="hidden print-only mb-6 border-b-2 border-slate-900 pb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-black uppercase text-slate-900">Sistema Único de Saúde - SUS</h1>
                        <h2 class="text-sm font-bold text-slate-700">Centro de Especialidades Odontológicas - CEO Digital</h2>
                        <h3 class="text-xs text-slate-500">Relatório de Produção e Atendimentos Concluídos (Período: {{ \Carbon\Carbon::parse($dados['periodo']['inicio'])->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($dados['periodo']['fim'])->format('d/m/Y') }})</h3>
                    </div>
                    <div class="text-right text-xs text-slate-500">
                        <div>Emitido em: {{ now()->format('d/m/Y H:i') }}</div>
                        <div>Usuário: {{ Auth::user()->name }}</div>
                    </div>
                </div>
            </div>

            <!-- Filtros (no-print) -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs no-print">
                <form method="GET" action="{{ route('relatorios.producao') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Data Início</label>
                        <input type="date" name="data_inicio" value="{{ $filtros['data_inicio'] ?? $dados['periodo']['inicio'] }}" class="w-full py-1.5 border-slate-300 rounded text-xs focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Data Fim</label>
                        <input type="date" name="data_fim" value="{{ $filtros['data_fim'] ?? $dados['periodo']['fim'] }}" class="w-full py-1.5 border-slate-300 rounded text-xs focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Dentista</label>
                        <select name="dentista_id" class="w-full py-1.5 border-slate-300 rounded text-xs focus:ring-teal-500">
                            <option value="">Todos os Dentistas</option>
                            @foreach($dentistas as $dent)
                                <option value="{{ $dent->id }}" {{ ($filtros['dentista_id'] ?? '') == $dent->id ? 'selected' : '' }}>
                                    Dr(a). {{ $dent->nome_completo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Especialidade</label>
                        <select name="especialidade_id" class="w-full py-1.5 border-slate-300 rounded text-xs focus:ring-teal-500">
                            <option value="">Todas as Especialidades</option>
                            @foreach($especialidades as $esp)
                                <option value="{{ $esp->id }}" {{ ($filtros['especialidade_id'] ?? '') == $esp->id ? 'selected' : '' }}>{{ $esp->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 py-1.5 bg-slate-800 text-white rounded text-xs font-bold hover:bg-slate-900 transition">
                            Atualizar
                        </button>
                        <a href="{{ route('relatorios.producao') }}" class="px-3 py-1.5 bg-slate-100 text-slate-600 rounded text-xs font-bold hover:bg-slate-200 transition">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>

            <!-- Total Concluídos KPI -->
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total de Procedimentos e Consultas Concluídas</span>
                <div class="mt-2 text-3xl font-black text-teal-600">{{ $dados['total_concluidos'] }}</div>
                <span class="text-xs text-slate-400">atendimentos finalizados com sucesso no período</span>
            </div>

            <!-- Tabela de Produção por Profissional -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200 font-bold text-sm text-slate-900">
                    Produção Consolidada por Profissional Cirurgião-Dentista
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 min-w-[650px]">
                        <thead class="bg-slate-100 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3">Profissional / CRO</th>
                                <th class="px-4 py-3">Especialidade Odontológica</th>
                                <th class="px-4 py-3 text-center">Consultas Regulares</th>
                                <th class="px-4 py-3 text-center">Encaixes de Urgência</th>
                                <th class="px-4 py-3 text-center">Total Concluídos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($dados['producao_por_dentista'] as $item)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-bold text-slate-900">
                                        Dr(a). {{ $item->dentista_nome }}
                                        <div class="text-xs text-slate-400 font-mono">CRO: {{ $item->cro }}</div>
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-teal-800">
                                        {{ $item->especialidade_nome }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-mono text-slate-700">
                                        {{ $item->total_normais }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-mono font-bold text-rose-600">
                                        {{ $item->total_encaixes }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-mono font-black text-slate-900 text-base">
                                        {{ $item->total_concluidos }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-slate-400">
                                        Nenhum atendimento concluído encontrado para os filtros selecionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
