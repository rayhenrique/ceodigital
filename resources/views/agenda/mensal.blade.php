<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-lg bg-teal-100 text-teal-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2zM3 10h18M7 15h1m4 0h1m4 0h1m-10 4h1m4 0h1m4 0h1"/></svg>
                    </span>
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        Mapa Mensal da Agenda
                    </h2>
                </div>
                <p class="text-sm text-slate-500 mt-1">
                    Visão panorâmica para a gestão: ocupação diária, capacidade de vagas e lotação do CEO
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('agenda.index') }}" class="inline-flex items-center px-3.5 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg font-semibold text-xs uppercase tracking-wider hover:bg-slate-50 transition shadow-xs">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Agenda
                </a>
                <a href="{{ route('agenda.create') }}" class="inline-flex items-center px-3.5 py-2 bg-teal-600 text-white rounded-lg font-semibold text-xs uppercase tracking-wider hover:bg-teal-700 transition shadow-xs">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Novo Agendamento
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Abas de Alternância de Visão da Agenda -->
            <div class="flex border-b border-slate-200">
                <a href="{{ route('agenda.index') }}" class="py-3 px-5 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Visualização Diária (Operacional)</span>
                </a>
                <a href="{{ route('agenda.mensal') }}" class="py-3 px-5 text-sm font-bold border-b-2 border-teal-600 text-teal-700 bg-teal-50/40 rounded-t-lg transition flex items-center gap-2">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2zM3 10h18M7 15h1m4 0h1m4 0h1m-10 4h1m4 0h1m4 0h1"/></svg>
                    <span>Mapa Mensal (Gestão & Ocupação)</span>
                </a>
            </div>

            <!-- Cards de Métricas e KPIs do Mês -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- 1. Ocupação Geral -->
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Ocupação do Mês</span>
                        <span class="p-2 rounded-lg bg-teal-50 text-teal-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </span>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-slate-900">{{ $mapa['kpis']['taxa_ocupacao'] }}%</span>
                        <span class="text-xs font-medium text-slate-500">média mensal</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 mt-3 overflow-hidden">
                        <div class="h-2 rounded-full {{ $mapa['kpis']['taxa_ocupacao'] >= 90 ? 'bg-rose-500' : ($mapa['kpis']['taxa_ocupacao'] >= 75 ? 'bg-amber-500' : 'bg-teal-500') }}" style="width: {{ min(100, $mapa['kpis']['taxa_ocupacao']) }}%"></div>
                    </div>
                </div>

                <!-- 2. Vagas Agendadas vs Capacidade -->
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Volume de Vagas</span>
                        <span class="p-2 rounded-lg bg-blue-50 text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </span>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-slate-900">{{ $mapa['kpis']['total_agendados'] }}</span>
                        <span class="text-xs text-slate-500">de <strong>{{ $mapa['kpis']['capacidade_total'] }}</strong> vagas ofertadas</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-2">
                        Somatório de todas as grades de atendimento
                    </p>
                </div>

                <!-- 3. Dias com Lotação Esgotada -->
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Dias Lotados</span>
                        <span class="p-2 rounded-lg {{ $mapa['kpis']['dias_lotados'] > 0 ? 'bg-rose-50 text-rose-600' : 'bg-slate-50 text-slate-500' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </span>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black {{ $mapa['kpis']['dias_lotados'] > 0 ? 'text-rose-600' : 'text-slate-800' }}">{{ $mapa['kpis']['dias_lotados'] }}</span>
                        <span class="text-xs text-slate-500">dia(s) com 100% de ocupação</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-2">
                        Requer remanejamento ou novos encaixes
                    </p>
                </div>

                <!-- 4. Desempenho e Faltas -->
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Atendidos vs Faltas</span>
                        <span class="p-2 rounded-lg bg-emerald-50 text-emerald-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                    </div>
                    <div class="mt-2 flex items-baseline gap-3">
                        <span class="text-lg font-bold text-emerald-700">{{ $mapa['kpis']['total_concluidos'] }} <span class="text-xs font-normal text-slate-500">atendidos</span></span>
                        <span class="text-lg font-bold text-rose-700">{{ $mapa['kpis']['total_faltas'] }} <span class="text-xs font-normal text-slate-500">faltas</span></span>
                    </div>
                    <p class="text-xs text-slate-400 mt-2">
                        Taxa de absenteísmo: <strong class="text-slate-700">{{ $mapa['kpis']['taxa_absenteismo'] }}%</strong>
                    </p>
                </div>
            </div>

            <!-- Barra de Navegação Temporal e Filtros Gerenciais -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200 shadow-xs">
                <form method="GET" action="{{ route('agenda.mensal') }}" class="space-y-4">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        
                        <!-- Navegador de Mês Anterior / Próximo -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('agenda.mensal', array_merge(request()->query(), ['mes' => $mapa['data_anterior']->month, 'ano' => $mapa['data_anterior']->year])) }}" 
                               class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition" 
                               title="Mês Anterior ({{ $mapa['data_anterior']->translatedFormat('F/Y') }})">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </a>

                            <div class="text-center min-w-[200px]">
                                <h3 class="text-lg font-black text-slate-800 capitalize tracking-tight">
                                    {{ $mapa['mes_ano_texto'] }}
                                </h3>
                            </div>

                            <a href="{{ route('agenda.mensal', array_merge(request()->query(), ['mes' => $mapa['data_proxima']->month, 'ano' => $mapa['data_proxima']->year])) }}" 
                               class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition" 
                               title="Próximo Mês ({{ $mapa['data_proxima']->translatedFormat('F/Y') }})">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>

                            <a href="{{ route('agenda.mensal') }}" class="ml-2 px-3 py-1.5 bg-teal-50 text-teal-700 hover:bg-teal-100 border border-teal-200 text-xs font-bold rounded-lg transition">
                                Mês Atual
                            </a>
                        </div>

                        <!-- Filtros Específicos -->
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 flex-1 lg:max-w-2xl">
                            <!-- Especialidade -->
                            <div>
                                <select name="especialidade_id" onchange="this.form.submit()" class="w-full py-1.5 border-slate-300 rounded-lg text-xs focus:ring-teal-500 focus:border-teal-500">
                                    <option value="">Todas Especialidades</option>
                                    @foreach($especialidades as $esp)
                                        <option value="{{ $esp->id }}" {{ ($especialidadeId ?? '') == $esp->id ? 'selected' : '' }}>
                                            {{ $esp->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Dentista -->
                            <div>
                                <select name="dentista_id" onchange="this.form.submit()" class="w-full py-1.5 border-slate-300 rounded-lg text-xs focus:ring-teal-500 focus:border-teal-500">
                                    <option value="">Todos os Dentistas</option>
                                    @foreach($dentistas as $dent)
                                        <option value="{{ $dent->id }}" {{ ($dentistaId ?? '') == $dent->id ? 'selected' : '' }}>
                                            {{ $dent->nome_completo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Turno -->
                            <div>
                                <select name="turno" onchange="this.form.submit()" class="w-full py-1.5 border-slate-300 rounded-lg text-xs focus:ring-teal-500 focus:border-teal-500">
                                    <option value="">Todos os Turnos</option>
                                    <option value="manha" {{ ($turnoFiltro ?? '') === 'manha' ? 'selected' : '' }}>Manhã</option>
                                    <option value="tarde" {{ ($turnoFiltro ?? '') === 'tarde' ? 'selected' : '' }}>Tarde</option>
                                    <option value="noite" {{ ($turnoFiltro ?? '') === 'noite' ? 'selected' : '' }}>Noite</option>
                                </select>
                            </div>

                            <!-- Limpar -->
                            <div>
                                @if(request()->anyFilled(['especialidade_id', 'dentista_id', 'turno']))
                                    <a href="{{ route('agenda.mensal', ['mes' => $mesAtual, 'ano' => $anoAtual]) }}" class="w-full py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg flex items-center justify-center gap-1 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Limpar
                                    </a>
                                @endif
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            <!-- Grade do Calendário Mensal -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <!-- Cabeçalho dos Dias da Semana -->
                <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50 text-center text-xs font-bold uppercase tracking-wider text-slate-600">
                    <div class="py-3 border-r border-slate-200 last:border-r-0">Segunda</div>
                    <div class="py-3 border-r border-slate-200 last:border-r-0">Terça</div>
                    <div class="py-3 border-r border-slate-200 last:border-r-0">Quarta</div>
                    <div class="py-3 border-r border-slate-200 last:border-r-0">Quinta</div>
                    <div class="py-3 border-r border-slate-200 last:border-r-0">Sexta</div>
                    <div class="py-3 border-r border-slate-200 last:border-r-0 bg-slate-100/60 text-slate-400">Sábado</div>
                    <div class="py-3 bg-slate-100/60 text-slate-400">Domingo</div>
                </div>

                <!-- Células dos Dias -->
                <div class="grid grid-cols-7 divide-x divide-y divide-slate-200">
                    @foreach($mapa['dias_grade'] as $dia)
                        <div class="min-h-[115px] sm:min-h-[135px] p-2 sm:p-2.5 flex flex-col justify-between transition relative group 
                            {{ ! $dia['is_mes_atual'] ? 'bg-slate-50/70 opacity-40' : ($dia['is_hoje'] ? 'bg-teal-50/30' : 'bg-white hover:bg-slate-50/80') }}">
                            
                            <!-- Topo do Card do Dia -->
                            <div class="flex items-start justify-between gap-1">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-sm sm:text-base font-bold {{ $dia['is_hoje'] ? 'w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-teal-600 text-white flex items-center justify-center' : ($dia['is_mes_atual'] ? 'text-slate-800' : 'text-slate-400') }}">
                                        {{ $dia['dia'] }}
                                    </span>
                                    @if($dia['is_hoje'])
                                        <span class="hidden sm:inline-block text-[10px] uppercase font-bold text-teal-700 bg-teal-100 px-1.5 py-0.5 rounded">Hoje</span>
                                    @endif
                                </div>

                                <!-- Badge de Status de Lotação -->
                                @if($dia['is_mes_atual'] && $dia['capacidade'] > 0)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold border {{ $dia['cor_badge'] }}">
                                        {{ $dia['texto_status'] }}
                                    </span>
                                @elseif($dia['is_mes_atual'] && $dia['capacidade'] === 0)
                                    <span class="text-[10px] text-slate-400 font-medium hidden sm:inline">Folga / Sem Escala</span>
                                @endif
                            </div>

                            <!-- Miolo do Card: Ocupação e Números -->
                            <div class="my-1 sm:my-2 space-y-1">
                                @if($dia['is_mes_atual'] && $dia['capacidade'] > 0)
                                    <div class="flex items-baseline justify-between text-xs">
                                        <span class="font-bold text-slate-800">
                                            {{ $dia['total_agendados'] }} <span class="text-slate-400 font-normal">/ {{ $dia['capacidade'] }}</span>
                                        </span>
                                        <span class="text-[11px] font-semibold text-slate-500">
                                            {{ $dia['percentual_ocupacao'] }}%
                                        </span>
                                    </div>

                                    <!-- Barra Visual de Ocupação -->
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-1.5 rounded-full {{ $dia['cor_barra'] }}" style="width: {{ min(100, $dia['percentual_ocupacao']) }}%"></div>
                                    </div>

                                    <!-- Distribuição por Turnos (Pills discretos) -->
                                    <div class="hidden sm:flex items-center gap-1 text-[10px] text-slate-500 font-mono mt-1">
                                        @if($dia['turnos']['manha'] > 0)
                                            <span class="bg-slate-100 px-1 py-0.5 rounded" title="Manhã: {{ $dia['turnos']['manha'] }}">M:{{ $dia['turnos']['manha'] }}</span>
                                        @endif
                                        @if($dia['turnos']['tarde'] > 0)
                                            <span class="bg-slate-100 px-1 py-0.5 rounded" title="Tarde: {{ $dia['turnos']['tarde'] }}">T:{{ $dia['turnos']['tarde'] }}</span>
                                        @endif
                                        @if($dia['turnos']['noite'] > 0)
                                            <span class="bg-slate-100 px-1 py-0.5 rounded" title="Noite: {{ $dia['turnos']['noite'] }}">N:{{ $dia['turnos']['noite'] }}</span>
                                        @endif
                                        @if($dia['total_encaixes'] > 0)
                                            <span class="bg-amber-100 text-amber-800 px-1 py-0.5 rounded font-bold" title="{{ $dia['total_encaixes'] }} Encaixe(s)">+{{ $dia['total_encaixes'] }} enc</span>
                                        @endif
                                    </div>
                                @elseif($dia['is_mes_atual'] && $dia['total_agendados'] > 0)
                                    <!-- Caso tenha agendamento mesmo em dia sem escala padrão -->
                                    <div class="text-xs text-amber-800 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">
                                        <strong>{{ $dia['total_agendados'] }}</strong> agendado(s)
                                    </div>
                                @endif
                            </div>

                            <!-- Rodapé do Card: Link direto para a Agenda Diária -->
                            <div class="pt-1 border-t border-slate-100/80 flex items-center justify-between">
                                <a href="{{ route('agenda.index', ['data' => $dia['data']]) }}" 
                                   class="text-[11px] font-bold text-teal-700 hover:text-teal-900 transition flex items-center gap-0.5">
                                    <span>Abrir dia</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Legenda Explicativa do Gestor -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-4 text-xs text-slate-600">
                <div class="font-bold text-slate-800 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Legenda de Lotação:</span>
                </div>
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <span><strong>Vagas Livres:</strong> Menos de 50% ocupado</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                        <span><strong>Moderado:</strong> 50% a 79% ocupado</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        <span><strong>Quase Cheio:</strong> 80% a 99% ocupado</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                        <span><strong>Lotado:</strong> 100% de ocupação ou acima</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-slate-300"></span>
                        <span><strong>Sem Escala:</strong> Finais de semana ou sem expediente</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
