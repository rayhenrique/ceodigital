<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Relatório de Absenteísmo & Faltas
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Indicador de absenteísmo SUS por UBS de origem e especialidade (RF22)
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
                        <h3 class="text-xs text-slate-500">Relatório Oficial de Absenteísmo e Faltas (Período: {{ \Carbon\Carbon::parse($dados['periodo']['inicio'])->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($dados['periodo']['fim'])->format('d/m/Y') }})</h3>
                    </div>
                    <div class="text-right text-xs text-slate-500">
                        <div>Emitido em: {{ now()->format('d/m/Y H:i') }}</div>
                        <div>Usuário: {{ Auth::user()->name }}</div>
                    </div>
                </div>
            </div>

            <!-- Filtros (no-print) -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs no-print">
                <form method="GET" action="{{ route('relatorios.absenteismo') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Data Início</label>
                        <input type="date" name="data_inicio" value="{{ $filtros['data_inicio'] ?? $dados['periodo']['inicio'] }}" class="w-full py-1.5 border-slate-300 rounded text-xs focus:ring-rose-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Data Fim</label>
                        <input type="date" name="data_fim" value="{{ $filtros['data_fim'] ?? $dados['periodo']['fim'] }}" class="w-full py-1.5 border-slate-300 rounded text-xs focus:ring-rose-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Especialidade</label>
                        <select name="especialidade_id" class="w-full py-1.5 border-slate-300 rounded text-xs focus:ring-rose-500">
                            <option value="">Todas</option>
                            @foreach($especialidades as $esp)
                                <option value="{{ $esp->id }}" {{ ($filtros['especialidade_id'] ?? '') == $esp->id ? 'selected' : '' }}>{{ $esp->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">UBS de Origem</label>
                        <select name="ubs_id" class="w-full py-1.5 border-slate-300 rounded text-xs focus:ring-rose-500">
                            <option value="">Todas as UBSs</option>
                            @foreach($ubsList as $u)
                                <option value="{{ $u->id }}" {{ ($filtros['ubs_id'] ?? '') == $u->id ? 'selected' : '' }}>{{ $u->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 py-1.5 bg-slate-800 text-white rounded text-xs font-bold hover:bg-slate-900 transition">
                            Atualizar
                        </button>
                        <a href="{{ route('relatorios.absenteismo') }}" class="px-3 py-1.5 bg-slate-100 text-slate-600 rounded text-xs font-bold hover:bg-slate-200 transition">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>

            <!-- KPIs de Absenteísmo -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total de Agendamentos</span>
                    <div class="mt-2 text-3xl font-black text-slate-900">{{ $dados['total_agendamentos'] }}</div>
                    <span class="text-xs text-slate-400">no período selecionado</span>
                </div>

                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total de Faltas</span>
                    <div class="mt-2 text-3xl font-black text-rose-600">{{ $dados['total_faltas'] }}</div>
                    <span class="text-xs text-slate-400">pacientes não compareceram</span>
                </div>

                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Taxa Geral de Absenteísmo</span>
                    <div class="mt-2 text-3xl font-black text-amber-600">{{ $dados['taxa_absenteismo_geral'] }}%</div>
                    <span class="text-xs text-slate-400">índice de ociosidade das vagas</span>
                </div>
            </div>

            <!-- Tabela 1: Consolidado por UBS -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200 font-bold text-sm text-slate-900">
                    1. Taxa de Faltas por Unidade Básica de Saúde (UBS de Origem)
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-100 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3">Unidade Básica de Saúde</th>
                                <th class="px-4 py-3 text-center">Agendados</th>
                                <th class="px-4 py-3 text-center">Faltas</th>
                                <th class="px-4 py-3 text-center">Taxa de Absenteísmo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($dados['consolidado_por_ubs'] as $item)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-bold text-slate-900">{{ $item['ubs_nome'] }}</td>
                                    <td class="px-4 py-3 text-center">{{ $item['total_agendados'] }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-rose-600">{{ $item['total_faltas'] }}</td>
                                    <td class="px-4 py-3 text-center font-bold">
                                        <span class="px-2 py-0.5 rounded text-xs {{ $item['taxa_absenteismo'] > 20 ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700' }}">
                                            {{ $item['taxa_absenteismo'] }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-slate-400">Nenhum agendamento registrado para o período.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabela 2: Relação Nominal de Pacientes Faltosos -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200 font-bold text-sm text-slate-900">
                    2. Relação Nominal de Pacientes que Faltaram
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-100 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3">Data / Turno</th>
                                <th class="px-4 py-3">Paciente / CPF</th>
                                <th class="px-4 py-3">Telefone</th>
                                <th class="px-4 py-3">UBS Encaminhadora</th>
                                <th class="px-4 py-3">Especialidade / Dentista</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($dados['faltas_nominais'] as $falta)
                                <tr class="hover:bg-slate-50 text-xs">
                                    <td class="px-4 py-3 whitespace-nowrap font-bold text-slate-800">
                                        {{ $falta->data_agendamento->format('d/m/Y') }} ({{ ucfirst($falta->turno) }})
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-900">{{ $falta->paciente->nome_completo }}</div>
                                        <div class="text-slate-400 font-mono">{{ $falta->paciente->cpf_formatado }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">{{ $falta->paciente->telefone_1 }}</td>
                                    <td class="px-4 py-3">{{ $falta->paciente->ubs->nome ?? 'N/D' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-teal-800">{{ $falta->especialidade->nome }}</div>
                                        <div class="text-slate-500">Dr(a). {{ $falta->dentista->nome }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-slate-400">Nenhuma falta nominal registrada no período.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
