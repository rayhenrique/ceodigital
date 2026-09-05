<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Relatório de Demanda Reprimida & Tempo de Espera
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Análise regulatória da fila de espera e tempo médio em dias por especialidade
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
                        <h3 class="text-xs text-slate-500">Relatório de Demanda Reprimida e Fila de Espera</h3>
                    </div>
                    <div class="text-right text-xs text-slate-500">
                        <div>Emitido em: {{ now()->format('d/m/Y H:i') }}</div>
                        <div>Usuário: {{ Auth::user()->name }}</div>
                    </div>
                </div>
            </div>

            <!-- Filtros (no-print) -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs no-print">
                <form method="GET" action="{{ route('relatorios.demanda-reprimida') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Especialidade</label>
                        <select name="especialidade_id" class="w-full py-1.5 border-slate-300 rounded text-xs focus:ring-purple-500">
                            <option value="">Todas as Especialidades</option>
                            @foreach($especialidades as $esp)
                                <option value="{{ $esp->id }}" {{ ($filtros['especialidade_id'] ?? '') == $esp->id ? 'selected' : '' }}>{{ $esp->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Prioridade Clínica</label>
                        <select name="prioridade" class="w-full py-1.5 border-slate-300 rounded text-xs focus:ring-purple-500">
                            <option value="">Todas as Prioridades</option>
                            <option value="urgente" {{ ($filtros['prioridade'] ?? '') === 'urgente' ? 'selected' : '' }}>Urgente</option>
                            <option value="normal" {{ ($filtros['prioridade'] ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 py-1.5 bg-slate-800 text-white rounded text-xs font-bold hover:bg-slate-900 transition">
                            Atualizar
                        </button>
                        <a href="{{ route('relatorios.demanda-reprimida') }}" class="px-3 py-1.5 bg-slate-100 text-slate-600 rounded text-xs font-bold hover:bg-slate-200 transition">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>

            <!-- KPIs Demanda Reprimida -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total na Espera</span>
                    <div class="mt-2 text-3xl font-black text-purple-600">{{ $dados['total_aguardando'] }}</div>
                    <span class="text-xs text-slate-400">pacientes aguardando</span>
                </div>

                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Casos Urgentes</span>
                    <div class="mt-2 text-3xl font-black text-rose-600">{{ $dados['total_urgentes'] }}</div>
                    <span class="text-xs text-slate-400">prioridade máxima</span>
                </div>

                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Fila Cronológica Normal</span>
                    <div class="mt-2 text-3xl font-black text-slate-800">{{ $dados['total_normais'] }}</div>
                    <span class="text-xs text-slate-400">ordem de solicitação</span>
                </div>

                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tempo Médio de Espera</span>
                    <div class="mt-2 text-3xl font-black text-amber-600">{{ $dados['tempo_medio_espera_dias'] }}</div>
                    <span class="text-xs text-slate-400">dias na fila regulatória</span>
                </div>
            </div>

            <!-- Tabela 1: Consolidado por Especialidade -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200 font-bold text-sm text-slate-900">
                    1. Demanda Reprimida por Especialidade Odontológica
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-100 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3">Especialidade Odontológica</th>
                                <th class="px-4 py-3 text-center">Casos Urgentes</th>
                                <th class="px-4 py-3 text-center">Total Aguardando</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($dados['por_especialidade'] as $item)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-bold text-slate-900">{{ $item->especialidade_nome }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-rose-600">{{ $item->urgentes }}</td>
                                    <td class="px-4 py-3 text-center font-black text-slate-900 text-base">{{ $item->total }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-slate-400">Nenhuma solicitação aguardando na fila.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabela 2: Relação Nominal da Lista de Espera -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200 font-bold text-sm text-slate-900">
                    2. Relação Nominal Ordenada por Prioridade e Data
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-100 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3">Posição / Prioridade</th>
                                <th class="px-4 py-3">Paciente / CPF</th>
                                <th class="px-4 py-3">UBS de Encaminhamento</th>
                                <th class="px-4 py-3">Especialidade</th>
                                <th class="px-4 py-3">Data Solicitação / Dias em Espera</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($dados['lista_espera'] as $index => $item)
                                <tr class="hover:bg-slate-50 text-xs">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-mono font-bold text-slate-400">#{{ $index + 1 }}</span>
                                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $item->prioridade === 'urgente' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700' }}">
                                                {{ ucfirst($item->prioridade) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-900">{{ $item->paciente->nome_completo }}</div>
                                        <div class="text-slate-400 font-mono">{{ $item->paciente->cpf_formatado }}</div>
                                    </td>
                                    <td class="px-4 py-3">{{ $item->paciente->ubs->nome ?? 'N/D' }}</td>
                                    <td class="px-4 py-3 font-semibold text-purple-900">{{ $item->especialidade->nome }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="font-bold text-slate-800">{{ $item->data_solicitacao->format('d/m/Y') }}</span>
                                        <span class="text-slate-500 block">({{ $item->created_at->diffForHumans() }})</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-slate-400">Nenhum registro encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
