<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Especialidades Odontológicas
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Serviços clínicos de média complexidade ofertados pelo CEO
                </p>
            </div>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('especialidades.create') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 transition shadow-xs">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nova Especialidade
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Search Card -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <form method="GET" action="{{ route('especialidades.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="busca" value="{{ $busca ?? '' }}" placeholder="Pesquisar especialidade por nome..." class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="submit" class="flex-1 sm:flex-initial px-5 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-900 transition">
                            Buscar
                        </button>
                        @if($busca)
                            <a href="{{ route('especialidades.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-200 transition text-center">
                                Limpar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 min-w-[700px]">
                        <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3.5">Especialidade</th>
                                <th class="px-4 py-3.5">Descrição Clínica</th>
                                <th class="px-4 py-3.5 text-center">Dentistas Vinculados</th>
                                <th class="px-4 py-3.5 text-center">Consultas / Fila</th>
                                <th class="px-4 py-3.5 text-center">Status</th>
                                @if(Auth::user()->isAdmin())
                                    <th class="px-4 py-3.5 text-right">Ações</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($especialidades as $esp)
                                @php
                                    $temPacientes = ($esp->agendamentos_count > 0 || $esp->demandas_reprimidas_count > 0);
                                    $temDentistas = ($esp->dentistas_count > 0);
                                    $podeExcluir = (!$temPacientes && !$temDentistas);
                                @endphp
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-3.5 font-bold text-slate-900">
                                        {{ $esp->nome }}
                                    </td>
                                    <td class="px-4 py-3.5 text-xs text-slate-600 max-w-xs">
                                        {{ $esp->descricao ?: 'Sem descrição informada.' }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-teal-50 text-teal-800">
                                            {{ $esp->dentistas_count }} profissional(is)
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-800" title="Consultas agendadas de pacientes">
                                                {{ $esp->agendamentos_count }} agendada(s)
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-50 text-purple-800" title="Pacientes na fila de espera">
                                                {{ $esp->demandas_reprimidas_count }} na fila
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        @if($esp->status_ativo)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                Ativa
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-200 text-slate-600">
                                                Inativa
                                            </span>
                                        @endif
                                    </td>
                                    @if(Auth::user()->isAdmin())
                                        <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <a href="{{ route('especialidades.edit', $esp) }}" class="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition inline-flex items-center gap-1 text-xs font-semibold" title="Editar Especialidade">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    <span>Editar</span>
                                                </a>

                                                @if($podeExcluir)
                                                    <form method="POST" action="{{ route('especialidades.destroy', $esp) }}" onsubmit="return confirm('Deseja realmente excluir a especialidade \'{{ addslashes($esp->nome) }}\'? Esta ação não poderá ser desfeita.')" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition inline-flex items-center gap-1 text-xs font-semibold" title="Excluir Especialidade">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                            <span>Excluir</span>
                                                        </button>
                                                    </form>
                                                @else
                                                    @php
                                                        $motivoBloqueio = [];
                                                        if ($esp->agendamentos_count > 0) $motivoBloqueio[] = "{$esp->agendamentos_count} agendamento(s)";
                                                        if ($esp->demandas_reprimidas_count > 0) $motivoBloqueio[] = "{$esp->demandas_reprimidas_count} paciente(s) na fila";
                                                        if ($esp->dentistas_count > 0) $motivoBloqueio[] = "{$esp->dentistas_count} dentista(s)";
                                                        $textoBloqueio = implode(', ', $motivoBloqueio);
                                                    @endphp
                                                    <button type="button" 
                                                            onclick="alert('A especialidade \'{{ addslashes($esp->nome) }}\' não pode ser excluída pois possui vínculos ativos: {{ $textoBloqueio }}.')"
                                                            class="p-1.5 text-slate-300 hover:text-slate-400 rounded-lg transition cursor-not-allowed inline-flex items-center gap-1 text-xs font-semibold" 
                                                            title="Não é possível excluir: possui vínculos ({{ $textoBloqueio }})">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        <span>Excluir</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->isAdmin() ? 6 : 5 }}" class="px-4 py-8 text-center text-slate-400">
                                        Nenhuma especialidade cadastrada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($especialidades->hasPages())
                    <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                        {{ $especialidades->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
