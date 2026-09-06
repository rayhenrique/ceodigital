<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        Painel Operacional do CEO
                    </h2>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider bg-teal-100 text-teal-800">
                        Turno {{ ucfirst($turnoCorrente) }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-1">
                    {{ now()->translatedFormat('l, d \d\e F \d\e Y') }} &bull; Gestão da Atenção Odontológica Especializada
                </p>
            </div>
            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('agenda.create') }}" class="inline-flex items-center px-3.5 py-2 bg-teal-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 active:bg-teal-900 transition shadow-xs">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Novo Agendamento
                </a>
                <a href="{{ route('agenda.index') }}" class="inline-flex items-center px-3.5 py-2 bg-white border border-slate-300 rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-50 transition shadow-xs">
                    <svg class="w-4 h-4 me-1.5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Ver Agenda Completa
                </a>
                <a href="{{ route('pacientes.create') }}" class="inline-flex items-center px-3.5 py-2 bg-white border border-slate-300 rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-50 transition shadow-xs">
                    <svg class="w-4 h-4 me-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Cadastrar Paciente
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- KPIs Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
                <!-- Hoje: Total Agendados -->
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Agendados Hoje</span>
                        <div class="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-slate-900">{{ $metricas['hoje']['total_agendados'] }}</span>
                        <span class="text-xs text-slate-500">pacientes</span>
                    </div>
                </div>

                <!-- Hoje: Presentes / Recepção -->
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Presentes</span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-emerald-600">{{ $metricas['hoje']['presentes'] }}</span>
                        <span class="text-xs text-slate-500">na clínica</span>
                    </div>
                </div>

                <!-- Hoje: Concluídos -->
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Concluídos</span>
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-blue-600">{{ $metricas['hoje']['concluidos'] }}</span>
                        <span class="text-xs text-slate-500">atendidos</span>
                    </div>
                </div>

                <!-- Hoje: Encaixes -->
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Encaixes</span>
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-amber-600">{{ $metricas['hoje']['encaixes'] }}</span>
                        <span class="text-xs text-slate-500">urgências</span>
                    </div>
                </div>

                <!-- Faltas Hoje -->
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Faltas Hoje</span>
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-rose-600">{{ $metricas['hoje']['faltas'] }}</span>
                        <span class="text-xs text-slate-500">{{ $metricas['mes']['absenteismo_percentual'] }}% no mês</span>
                    </div>
                </div>

                <!-- Fila de Espera -->
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Fila Reprimida</span>
                        <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-purple-600">{{ $metricas['demanda_reprimida_total'] }}</span>
                        <span class="text-xs text-slate-500">aguardando</span>
                    </div>
                </div>
            </div>

            <!-- Fila do Dia (Recepção & Atendimento) -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/50">
                    <div>
                        <h3 class="font-bold text-lg text-slate-900 flex items-center gap-2">
                            <span>Fluxo de Atendimento de Hoje</span>
                            <span class="text-xs font-semibold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">
                                {{ $atendimentosHoje->count() }} agendamento(s)
                            </span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Controle em tempo real de chegada, triagem e conclusão dos atendimentos
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('agenda.index', ['data' => now()->format('Y-m-d')]) }}" class="text-xs font-semibold text-teal-700 hover:text-teal-900 underline flex items-center gap-1">
                            <span>Abrir na tela da Agenda</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

                @if($atendimentosHoje->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h4 class="text-base font-bold text-slate-800">Nenhum atendimento agendado para hoje</h4>
                        <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                            Ainda não há pacientes agendados para a data de hoje. Você pode criar um agendamento regular ou um encaixe imediato.
                        </p>
                        <div class="mt-4 flex justify-center gap-3">
                            <a href="{{ route('agenda.create') }}" class="px-4 py-2 bg-teal-600 text-white text-xs font-bold rounded-lg hover:bg-teal-700 transition">
                                Agendar Paciente
                            </a>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600 min-w-[700px]">
                            <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3">Turno / Tipo</th>
                                    <th class="px-4 py-3">Paciente</th>
                                    <th class="px-4 py-3">Dentista / Especialidade</th>
                                    <th class="px-4 py-3 text-center">Chegada</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-right">Ações Rápidas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($atendimentosHoje as $agendamento)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <!-- Turno / Tipo -->
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center gap-1.5">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold {{ $agendamento->turno === 'manha' ? 'bg-amber-100 text-amber-800' : ($agendamento->turno === 'tarde' ? 'bg-orange-100 text-orange-800' : 'bg-indigo-100 text-indigo-800') }}">
                                                    {{ ucfirst($agendamento->turno) }}
                                                </span>
                                                @if($agendamento->tipo === 'encaixe')
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">
                                                        ENCAIXE
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Paciente -->
                                        <td class="px-4 py-3">
                                            <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                                <a href="{{ route('pacientes.show', $agendamento->paciente) }}" class="hover:text-teal-600 transition">
                                                    {{ $agendamento->paciente->nome }}
                                                </a>
                                            </div>
                                            <div class="text-xs text-slate-500 flex items-center gap-2 mt-0.5">
                                                <span>CPF: {{ $agendamento->paciente->cpf_formatado }}</span>
                                                &bull;
                                                <span>UBS: {{ $agendamento->paciente->ubs->nome ?? 'N/D' }}</span>
                                            </div>
                                        </td>

                                        <!-- Dentista / Especialidade -->
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-slate-800">{{ $agendamento->dentista->nome }}</div>
                                            <div class="text-xs text-teal-700 font-semibold">{{ $agendamento->especialidade->nome }}</div>
                                        </td>

                                        <!-- Horário Chegada -->
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            @if($agendamento->horario_chegada)
                                                <span class="inline-flex items-center gap-1 text-xs font-mono font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    {{ \Carbon\Carbon::parse($agendamento->horario_chegada)->format('H:i') }}
                                                </span>
                                            @else
                                                <span class="text-xs text-slate-400 italic">Não registrado</span>
                                            @endif
                                        </td>

                                        <!-- Status Badge -->
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            @php
                                                $statusClasses = [
                                                    'agendado' => 'bg-slate-100 text-slate-800 border-slate-200',
                                                    'presente' => 'bg-emerald-100 text-emerald-800 border-emerald-300 animate-pulse',
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
                                                    'falta' => 'Falta',
                                                    'cancelado' => 'Cancelado',
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusClasses[$agendamento->status] ?? 'bg-slate-100 text-slate-700' }}">
                                                {{ $statusLabels[$agendamento->status] ?? ucfirst($agendamento->status) }}
                                            </span>
                                        </td>

                                        <!-- Ações Rápidas -->
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                @if($agendamento->status === 'agendado')
                                                    <form method="POST" action="{{ route('agenda.chegada', $agendamento) }}">
                                                        @csrf
                                                        <button type="submit" class="px-2.5 py-1 bg-emerald-600 text-white rounded text-xs font-bold hover:bg-emerald-700 transition flex items-center gap-1 shadow-xs" title="Confirmar Presença do Paciente na Recepção">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                            Chegou
                                                        </button>
                                                    </form>
                                                @elseif($agendamento->status === 'presente')
                                                    <form method="POST" action="{{ route('agenda.status', $agendamento) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="em_atendimento">
                                                        <button type="submit" class="px-2.5 py-1 bg-blue-600 text-white rounded text-xs font-bold hover:bg-blue-700 transition flex items-center gap-1 shadow-xs" title="Chamar para Consultório">
                                                            Atender
                                                        </button>
                                                    </form>
                                                @elseif($agendamento->status === 'em_atendimento')
                                                    <form method="POST" action="{{ route('agenda.status', $agendamento) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="concluido">
                                                        <button type="submit" class="px-2.5 py-1 bg-teal-700 text-white rounded text-xs font-bold hover:bg-teal-800 transition flex items-center gap-1 shadow-xs" title="Finalizar Atendimento">
                                                            Concluir
                                                        </button>
                                                    </form>
                                                @endif

                                                <a href="{{ route('agenda.index', ['data' => $agendamento->data_agendamento->format('Y-m-d'), 'dentista_id' => $agendamento->dentista_id]) }}" class="p-1 text-slate-400 hover:text-slate-700 transition" title="Ver detalhes na grade">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
