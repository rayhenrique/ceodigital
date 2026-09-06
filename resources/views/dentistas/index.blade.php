<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Corpo Clínico & Dentistas
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Especialistas odontológicos e escalas operacionais do CEO
                </p>
            </div>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('dentistas.create') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 transition shadow-xs">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Novo Dentista
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Search Card -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <form method="GET" action="{{ route('dentistas.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="busca" value="{{ $busca ?? '' }}" placeholder="Pesquisar por Nome do Dentista ou CRO..." class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="submit" class="flex-1 sm:flex-initial px-5 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-900 transition">
                            Buscar
                        </button>
                        @if($busca)
                            <a href="{{ route('dentistas.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-200 transition text-center">
                                Limpar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Dentistas Grid Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($dentistas as $dentista)
                    <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-5 flex flex-col justify-between hover:shadow-md transition">
                        <div>
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-bold text-slate-900 text-base">
                                        <a href="{{ route('dentistas.show', $dentista) }}" class="hover:text-teal-600 transition">
                                            Dr(a). {{ $dentista->nome_completo }}
                                        </a>
                                    </h3>
                                    <div class="text-xs font-mono text-slate-500 mt-0.5">
                                        CRO: {{ $dentista->cro }}
                                    </div>
                                </div>
                                @if($dentista->status_ativo)
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                        Ativo
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-200 text-slate-600">
                                        Inativo
                                    </span>
                                @endif
                            </div>

                            <div class="mt-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-teal-50 text-teal-800 border border-teal-100">
                                    {{ $dentista->especialidade->nome ?? 'Clínico Geral' }}
                                </span>
                            </div>

                            <!-- Grade / Escala Semanal Resumida -->
                            <div class="mt-4 pt-3 border-t border-slate-100">
                                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Escala Semanal de Turnos</div>
                                @if($dentista->grades->isEmpty())
                                    <p class="text-xs text-slate-400 italic">Nenhum turno configurado na grade.</p>
                                @else
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($dentista->grades as $grade)
                                            <span class="text-[11px] font-medium bg-slate-100 text-slate-700 px-2 py-0.5 rounded">
                                                {{ $grade->dia_semana_texto }} ({{ ucfirst(substr($grade->turno, 0, 1)) }}) &bull; {{ $grade->vagas_padrao }} v.
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs text-slate-500">
                                <strong>{{ $dentista->agendamentos_count }}</strong> atendimentos
                            </span>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('dentistas.show', $dentista) }}" class="text-xs font-semibold text-slate-600 hover:text-teal-700">
                                    Ver Perfil
                                </a>
                                @if(Auth::user()->isAdmin())
                                    <span class="text-slate-300">&bull;</span>
                                    <a href="{{ route('dentistas.edit', $dentista) }}" class="text-xs font-semibold text-teal-600 hover:text-teal-800">
                                        Editar
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-xl border border-slate-200 p-8 text-center text-slate-400">
                        Nenhum profissional encontrado com os filtros informados.
                    </div>
                @endforelse
            </div>

            @if($dentistas->hasPages())
                <div class="bg-white rounded-xl border border-slate-200 p-4">
                    {{ $dentistas->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
