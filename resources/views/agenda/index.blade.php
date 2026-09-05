<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        Agenda Operacional do CEO
                    </h2>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider bg-teal-100 text-teal-800">
                        {{ $dataCarbon->translatedFormat('l, d \d\e F \d\e Y') }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-1">
                    Gestão diária de fluxo, controle de recepção, chamadas de consultório e encaixes
                </p>
            </div>

            <!-- Action buttons -->
            <div class="flex flex-wrap items-center gap-2" x-data>
                <!-- Botão Encaixe Modal -->
                <button @click="$dispatch('abrir-modal-encaixe')" class="inline-flex items-center px-3.5 py-2 bg-rose-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-rose-700 transition shadow-xs">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    + Encaixe Imediato
                </button>

                <!-- Botão Novo Agendamento -->
                <a href="{{ route('agenda.create') }}" class="inline-flex items-center px-3.5 py-2 bg-teal-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 transition shadow-xs">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Novo Agendamento
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        modalEncaixeAberto: false,
        modalChegadaAberto: false,
        agendamentoSelecionado: null,
        buscaPaciente: '',
        pacientesEncontrados: [],
        pacienteIdSelecionado: null,
        pacienteNomeSelecionado: '',
        buscando: false,

        buscarPacientes() {
            if (this.buscaPaciente.length < 2) {
                this.pacientesEncontrados = [];
                return;
            }
            this.buscando = true;
            fetch('{{ route('pacientes.index') }}?busca=' + encodeURIComponent(this.buscaPaciente), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                this.pacientesEncontrados = data;
                this.buscando = false;
            })
            .catch(() => { this.buscando = false; });
        },

        selecionarPaciente(p) {
            this.pacienteIdSelecionado = p.id;
            this.pacienteNomeSelecionado = p.nome + ' (CPF: ' + (p.cpf || 'S/N') + ')';
            this.pacientesEncontrados = [];
            this.buscaPaciente = '';
        }
    }" @abrir-modal-encaixe.window="modalEncaixeAberto = true">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Barra Superior de Controle: Navegação de Datas e Filtro de Dentista -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
                <!-- Seletor de Data -->
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <a href="{{ route('agenda.index', ['data' => $dataCarbon->copy()->subDay()->toDateString(), 'turno' => $turnoAtivo, 'dentista_id' => $dentistaId]) }}" class="p-2 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 transition" title="Dia Anterior">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>

                    <form method="GET" action="{{ route('agenda.index') }}" class="flex items-center gap-2">
                        <input type="hidden" name="turno" value="{{ $turnoAtivo }}">
                        @if($dentistaId)
                            <input type="hidden" name="dentista_id" value="{{ $dentistaId }}">
                        @endif
                        <input type="date" name="data" value="{{ $dataStr }}" onchange="this.form.submit()" class="border border-slate-300 rounded-lg text-sm py-1.5 px-3 font-semibold text-slate-800 focus:ring-teal-500 focus:border-teal-500">
                    </form>

                    <a href="{{ route('agenda.index', ['data' => $dataCarbon->copy()->addDay()->toDateString(), 'turno' => $turnoAtivo, 'dentista_id' => $dentistaId]) }}" class="p-2 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 transition" title="Próximo Dia">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    @if($dataStr !== now()->toDateString())
                        <a href="{{ route('agenda.index', ['data' => now()->toDateString(), 'turno' => $turnoAtivo, 'dentista_id' => $dentistaId]) }}" class="px-3 py-1.5 bg-teal-50 text-teal-700 text-xs font-bold rounded-lg hover:bg-teal-100 transition">
                            Ir para Hoje
                        </a>
                    @endif
                </div>

                <!-- Filtro por Dentista -->
                <div class="w-full md:w-auto flex items-center gap-2">
                    <label class="text-xs font-bold text-slate-500 uppercase whitespace-nowrap">Dentista:</label>
                    <form method="GET" action="{{ route('agenda.index') }}" class="flex-1">
                        <input type="hidden" name="data" value="{{ $dataStr }}">
                        <input type="hidden" name="turno" value="{{ $turnoAtivo }}">
                        <select name="dentista_id" onchange="this.form.submit()" class="w-full md:w-64 border border-slate-300 rounded-lg text-xs py-1.5 focus:ring-teal-500 focus:border-teal-500">
                            <option value="">Todos os Dentistas Escalados</option>
                            @foreach($dentistasEscalados as $d)
                                <option value="{{ $d->id }}" {{ (string)$dentistaId === (string)$d->id ? 'selected' : '' }}>
                                    Dr(a). {{ $d->nome }} ({{ $d->especialidade->nome ?? 'Geral' }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            <!-- Abas Operacionais por Turno (Manhã, Tarde, Noite) -->
            <div class="border-b border-slate-200">
                <nav class="-mb-px flex space-x-4 sm:space-x-8" aria-label="Tabs">
                    <!-- Turno Manhã -->
                    <a href="{{ route('agenda.index', ['data' => $dataStr, 'turno' => 'manha', 'dentista_id' => $dentistaId]) }}"
                       class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition {{ $turnoAtivo === 'manha' ? 'border-amber-500 text-amber-700' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Manhã (07h às 12h)</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $turnoAtivo === 'manha' ? 'bg-amber-100 text-amber-900' : 'bg-slate-100 text-slate-600' }}">
                            {{ $contagemTurnos['manha'] }}
                        </span>
                    </a>

                    <!-- Turno Tarde -->
                    <a href="{{ route('agenda.index', ['data' => $dataStr, 'turno' => 'tarde', 'dentista_id' => $dentistaId]) }}"
                       class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition {{ $turnoAtivo === 'tarde' ? 'border-orange-500 text-orange-700' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Tarde (13h às 18h)</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $turnoAtivo === 'tarde' ? 'bg-orange-100 text-orange-900' : 'bg-slate-100 text-slate-600' }}">
                            {{ $contagemTurnos['tarde'] }}
                        </span>
                    </a>

                    <!-- Turno Noite -->
                    <a href="{{ route('agenda.index', ['data' => $dataStr, 'turno' => 'noite', 'dentista_id' => $dentistaId]) }}"
                       class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition {{ $turnoAtivo === 'noite' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <span>Noite (18h às 22h)</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $turnoAtivo === 'noite' ? 'bg-indigo-100 text-indigo-900' : 'bg-slate-100 text-slate-600' }}">
                            {{ $contagemTurnos['noite'] }}
                        </span>
                    </a>
                </nav>
            </div>

            <!-- Tabela Operacional de Atendimentos -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                @if($agendamentos->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h4 class="text-base font-bold text-slate-800">Nenhum paciente agendado neste turno</h4>
                        <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                            Não existem agendamentos para o turno da {{ $turnoAtivo }} no dia {{ $dataCarbon->format('d/m/Y') }}.
                        </p>
                        <div class="mt-4 flex justify-center gap-2">
                            <button @click="modalEncaixeAberto = true" class="px-4 py-2 bg-rose-600 text-white text-xs font-bold rounded-lg hover:bg-rose-700 transition">
                                Registrar Encaixe
                            </button>
                            <a href="{{ route('agenda.create') }}" class="px-4 py-2 bg-teal-600 text-white text-xs font-bold rounded-lg hover:bg-teal-700 transition">
                                Agendamento Regular
                            </a>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3.5">Ordem / Tipo</th>
                                    <th class="px-4 py-3.5">Paciente</th>
                                    <th class="px-4 py-3.5">Dentista / Especialidade</th>
                                    <th class="px-4 py-3.5 text-center">Chegada Recepção</th>
                                    <th class="px-4 py-3.5 text-center">Status Atual</th>
                                    <th class="px-4 py-3.5 text-right">Fluxo / Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($agendamentos as $index => $agendamento)
                                    <tr class="hover:bg-slate-50/80 transition {{ $agendamento->status === 'presente' ? 'bg-emerald-50/40' : ($agendamento->status === 'em_atendimento' ? 'bg-blue-50/40' : '') }}">
                                        <!-- Ordem / Tipo -->
                                        <td class="px-4 py-3.5 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono font-bold text-xs text-slate-400">#{{ $index + 1 }}</span>
                                                @if($agendamento->tipo === 'encaixe')
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">
                                                        ENCAIXE
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                                        NORMAL
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Paciente -->
                                        <td class="px-4 py-3.5">
                                            <a href="{{ route('pacientes.show', $agendamento->paciente) }}" class="font-bold text-slate-900 hover:text-teal-600 transition block">
                                                {{ $agendamento->paciente->nome_completo }}
                                            </a>
                                            <div class="text-xs text-slate-500 flex items-center gap-2 mt-0.5">
                                                <span>CPF: {{ $agendamento->paciente->cpf_formatado }}</span>
                                                &bull;
                                                <span>UBS: {{ $agendamento->paciente->ubs->nome ?? 'N/D' }}</span>
                                            </div>
                                            @if($agendamento->observacoes)
                                                <div class="text-[11px] text-amber-700 italic mt-0.5">Obs: {{ $agendamento->observacoes }}</div>
                                            @endif
                                        </td>

                                        <!-- Dentista / Especialidade -->
                                        <td class="px-4 py-3.5">
                                            <div class="font-medium text-slate-800">Dr(a). {{ $agendamento->dentista->nome }}</div>
                                            <div class="text-xs text-teal-700 font-semibold">{{ $agendamento->especialidade->nome }}</div>
                                        </td>

                                        <!-- Horário Chegada -->
                                        <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                            @if($agendamento->horario_chegada)
                                                <span class="inline-flex items-center gap-1 text-xs font-mono font-bold text-slate-800 bg-slate-100 px-2 py-1 rounded">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    {{ \Carbon\Carbon::parse($agendamento->horario_chegada)->format('H:i') }}
                                                </span>
                                            @else
                                                <form method="POST" action="{{ route('agenda.chegada', $agendamento) }}">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 rounded text-xs font-bold transition inline-flex items-center gap-1 shadow-xs">
                                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                        Registrar Chegada
                                                    </button>
                                                </form>
                                            @endif
                                        </td>

                                        <!-- Status Badge -->
                                        <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                            @php
                                                $statusClasses = [
                                                    'agendado' => 'bg-slate-100 text-slate-800 border-slate-200',
                                                    'presente' => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold animate-pulse',
                                                    'em_atendimento' => 'bg-blue-100 text-blue-800 border-blue-300 font-bold',
                                                    'concluido' => 'bg-teal-100 text-teal-900 border-teal-300',
                                                    'falta' => 'bg-rose-100 text-rose-800 border-rose-200',
                                                    'cancelado' => 'bg-slate-200 text-slate-600 border-slate-300',
                                                ];
                                                $statusLabels = [
                                                    'agendado' => 'Agendado',
                                                    'presente' => 'Na Recepção',
                                                    'em_atendimento' => 'Em Atendimento',
                                                    'concluido' => 'Concluído',
                                                    'falta' => 'Falta (Ausente)',
                                                    'cancelado' => 'Cancelado',
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusClasses[$agendamento->status] ?? 'bg-slate-100 text-slate-700' }}">
                                                {{ $statusLabels[$agendamento->status] ?? ucfirst($agendamento->status) }}
                                            </span>
                                        </td>

                                        <!-- Transições de Status / Ações -->
                                        <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <!-- Se presente -> Iniciar atendimento -->
                                                @if($agendamento->status === 'presente')
                                                    <form method="POST" action="{{ route('agenda.status', $agendamento) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="em_atendimento">
                                                        <button type="submit" class="px-2.5 py-1 bg-blue-600 text-white rounded text-xs font-bold hover:bg-blue-700 transition" title="Chamar para Consultório">
                                                            Atender
                                                        </button>
                                                    </form>
                                                @endif

                                                <!-- Se em atendimento -> Concluir -->
                                                @if($agendamento->status === 'em_atendimento')
                                                    <form method="POST" action="{{ route('agenda.status', $agendamento) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="concluido">
                                                        <button type="submit" class="px-2.5 py-1 bg-teal-700 text-white rounded text-xs font-bold hover:bg-teal-800 transition" title="Concluir Consulta">
                                                            Concluir
                                                        </button>
                                                    </form>
                                                @endif

                                                <!-- Se agendado -> Marcar Falta -->
                                                @if($agendamento->status === 'agendado')
                                                    <form method="POST" action="{{ route('agenda.status', $agendamento) }}" onsubmit="return confirm('Confirmar que o paciente faltou ao atendimento?')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="falta">
                                                        <button type="submit" class="px-2 py-1 bg-rose-50 text-rose-700 border border-rose-200 rounded text-xs font-semibold hover:bg-rose-100 transition" title="Registrar Falta">
                                                            Falta
                                                        </button>
                                                    </form>
                                                @endif

                                                <!-- Cancelar se não concluído -->
                                                @if(!in_array($agendamento->status, ['concluido', 'cancelado']))
                                                    <form method="POST" action="{{ route('agenda.destroy', $agendamento) }}" onsubmit="return confirm('Deseja cancelar este agendamento?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 transition" title="Cancelar Agendamento">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- MODAL ALPINE.JS: CADASTRO DE ENCAIXE IMEDIATO (RF14) -->
            <div x-show="modalEncaixeAberto" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                    <div x-show="modalEncaixeAberto" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-slate-900/60 transition-opacity"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="modalEncaixeAberto" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full p-6">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <h3 class="font-bold text-lg text-slate-900">Registrar Encaixe de Urgência</h3>
                            </div>
                            <button @click="modalEncaixeAberto = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                        </div>

                        <form method="POST" action="{{ route('agenda.store') }}" class="space-y-4 mt-4">
                            @csrf
                            <input type="hidden" name="tipo" value="encaixe">
                            <input type="hidden" name="data_agendamento" value="{{ $dataStr }}">
                            <input type="hidden" name="turno" value="{{ $turnoAtivo }}">
                            <input type="hidden" name="paciente_id" :value="pacienteIdSelecionado" required>

                            <!-- Busca Reativa de Paciente -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Paciente do Encaixe *</label>
                                <div class="relative">
                                    <input type="text" x-model="buscaPaciente" @input.debounce.300ms="buscarPacientes()" placeholder="Digite nome, CPF ou cartão SUS para buscar..." class="w-full rounded-lg border-slate-300 text-sm focus:ring-rose-500 focus:border-rose-500">
                                    <div x-show="buscando" class="absolute right-3 top-2.5 text-xs text-slate-400">Buscando...</div>
                                </div>

                                <!-- Lista de Autocomplete -->
                                <div x-show="pacientesEncontrados.length > 0" class="mt-1 border border-slate-200 rounded-lg bg-white shadow-lg max-h-48 overflow-y-auto divide-y divide-slate-100 z-50">
                                    <template x-for="p in pacientesEncontrados" :key="p.id">
                                        <div @click="selecionarPaciente(p)" class="p-2.5 text-xs cursor-pointer hover:bg-teal-50 flex items-center justify-between">
                                            <div>
                                                <div class="font-bold text-slate-800" x-text="p.nome"></div>
                                                <div class="text-[11px] text-slate-500">CPF: <span x-text="p.cpf"></span> &bull; UBS: <span x-text="p.ubs"></span></div>
                                            </div>
                                            <span class="text-[10px] font-bold text-teal-700 bg-teal-100 px-2 py-0.5 rounded">Selecionar</span>
                                        </div>
                                    </template>
                                </div>

                                <!-- Paciente Selecionado -->
                                <div x-show="pacienteIdSelecionado" class="mt-2 p-2.5 bg-emerald-50 border border-emerald-200 rounded-lg text-xs flex items-center justify-between">
                                    <div>
                                        <span class="text-emerald-700 font-bold">Paciente Selecionado:</span>
                                        <span class="text-slate-800 font-semibold ml-1" x-text="pacienteNomeSelecionado"></span>
                                    </div>
                                    <button type="button" @click="pacienteIdSelecionado = null; pacienteNomeSelecionado = ''" class="text-rose-600 hover:text-rose-800 font-bold">Trocar</button>
                                </div>
                            </div>

                            <!-- Dentista -->
                            <div>
                                <label for="dentista_id" class="block text-xs font-bold text-slate-700 uppercase mb-1">Dentista Responsável *</label>
                                <select name="dentista_id" id="dentista_id" required class="w-full rounded-lg border-slate-300 text-sm focus:ring-rose-500 focus:border-rose-500">
                                    <option value="">Selecione o dentista escalado...</option>
                                    @foreach($dentistasEscalados as $dent)
                                        <option value="{{ $dent->id }}" {{ (string)$dentistaId === (string)$dent->id ? 'selected' : '' }}>
                                            Dr(a). {{ $dent->nome }} ({{ $dent->especialidade->nome ?? 'Geral' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Informações fixas do Encaixe -->
                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-xs space-y-1">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Data:</span>
                                    <span class="font-bold text-slate-800">{{ $dataCarbon->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Turno:</span>
                                    <span class="font-bold text-slate-800 uppercase">{{ $turnoAtivo }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Regra de Encaixe:</span>
                                    <span class="text-rose-700 font-bold">Limite de 2 encaixes/turno</span>
                                </div>
                            </div>

                            <!-- Justificativa de Urgência -->
                            <div>
                                <label for="observacoes" class="block text-xs font-bold text-slate-700 uppercase mb-1">Justificativa Clínica da Urgência</label>
                                <textarea name="observacoes" id="observacoes" rows="2" placeholder="Descreva o motivo do encaixe de urgência (dor aguda, trauma, etc)..." class="w-full rounded-lg border-slate-300 text-xs focus:ring-rose-500 focus:border-rose-500"></textarea>
                            </div>

                            <!-- Botões do Modal -->
                            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                                <button type="button" @click="modalEncaixeAberto = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-xs font-bold hover:bg-slate-200 transition">
                                    Cancelar
                                </button>
                                <button type="submit" :disabled="!pacienteIdSelecionado" :class="!pacienteIdSelecionado ? 'opacity-50 cursor-not-allowed' : ''" class="px-5 py-2 bg-rose-600 text-white rounded-lg text-xs font-bold hover:bg-rose-700 transition shadow-xs">
                                    Confirmar Encaixe
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
