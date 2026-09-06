<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Fila de Espera (Demanda Reprimida)
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Regulação e ordenação cronológica/prioritária do acesso às especialidades odontológicas
                </p>
            </div>
            <a href="{{ route('demanda-reprimida.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 transition shadow-xs">
                <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Inserir na Fila
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        modalPromoverAberto: false,
        demandaId: null,
        pacienteNome: '',
        especialidadeNome: '',
        actionUrl: '',

        abrirModalPromover(id, nome, especialidade) {
            this.demandaId = id;
            this.pacienteNome = nome;
            this.especialidadeNome = especialidade;
            this.actionUrl = '{{ url('demanda-reprimida') }}/' + id + '/promover';
            this.modalPromoverAberto = true;
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Card -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <form method="GET" action="{{ route('demanda-reprimida.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Status da Fila</label>
                        <select name="status" class="w-full py-1.5 border-slate-300 rounded text-xs focus:ring-purple-500">
                            <option value="aguardando" {{ $status === 'aguardando' ? 'selected' : '' }}>Aguardando Vaga</option>
                            <option value="agendado" {{ $status === 'agendado' ? 'selected' : '' }}>Já Agendados (Promovidos)</option>
                            <option value="desistente" {{ $status === 'desistente' ? 'selected' : '' }}>Desistentes</option>
                            <option value="" {{ $status === '' ? 'selected' : '' }}>Todos os Registros</option>
                        </select>
                    </div>

                    <!-- Especialidade -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Especialidade</label>
                        <select name="especialidade_id" class="w-full py-1.5 border-slate-300 rounded text-xs focus:ring-purple-500">
                            <option value="">Todas as Especialidades</option>
                            @foreach($especialidades as $esp)
                                <option value="{{ $esp->id }}" {{ (string)$especialidadeId === (string)$esp->id ? 'selected' : '' }}>
                                    {{ $esp->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Prioridade -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Classificação de Risco</label>
                        <select name="prioridade" class="w-full py-1.5 border-slate-300 rounded text-xs focus:ring-purple-500">
                            <option value="">Todas as Prioridades</option>
                            <option value="urgente" {{ $prioridade === 'urgente' ? 'selected' : '' }}>Urgente</option>
                            <option value="normal" {{ $prioridade === 'normal' ? 'selected' : '' }}>Normal</option>
                        </select>
                    </div>

                    <!-- Ação -->
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 py-1.5 bg-slate-800 text-white rounded text-xs font-bold hover:bg-slate-900 transition">
                            Filtrar Fila
                        </button>
                        <a href="{{ route('demanda-reprimida.index') }}" class="px-3 py-1.5 bg-slate-100 text-slate-600 rounded text-xs font-bold hover:bg-slate-200 transition">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 min-w-[750px]">
                        <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3.5">Posição / Prioridade</th>
                                <th class="px-4 py-3.5">Paciente</th>
                                <th class="px-4 py-3.5">Especialidade Solicitada</th>
                                <th class="px-4 py-3.5">Data Solicitação / Espera</th>
                                <th class="px-4 py-3.5 text-center">Status</th>
                                <th class="px-4 py-3.5 text-right">Ação Regulatória</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($demandas as $index => $demanda)
                                <tr class="hover:bg-slate-50 transition {{ $demanda->prioridade === 'urgente' ? 'bg-rose-50/30' : '' }}">
                                    <!-- Posição / Prioridade -->
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono font-bold text-xs text-slate-400">#{{ $index + 1 }}</span>
                                            @if($demanda->prioridade === 'urgente')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200 animate-pulse">
                                                    URGENTE
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700">
                                                    NORMAL
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Paciente -->
                                    <td class="px-4 py-3.5">
                                        <a href="{{ route('pacientes.show', $demanda->paciente) }}" class="font-bold text-slate-900 hover:text-teal-600 transition block">
                                            {{ $demanda->paciente->nome_completo }}
                                        </a>
                                        <div class="text-xs text-slate-500 flex items-center gap-2 mt-0.5">
                                            <span>CPF: {{ $demanda->paciente->cpf_formatado }}</span>
                                            &bull;
                                            <span>UBS: {{ $demanda->paciente->ubs->nome ?? 'N/D' }}</span>
                                            &bull;
                                            <span>Tel: {{ $demanda->paciente->telefone_1 }}</span>
                                        </div>
                                        @if($demanda->observacoes)
                                            <div class="text-[11px] text-slate-500 italic mt-1">
                                                Obs: {{ $demanda->observacoes }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Especialidade e Turno Preferencial -->
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <span class="font-bold text-slate-800">{{ $demanda->especialidade->nome }}</span>
                                        <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1">
                                            <span class="text-slate-400">Turno:</span>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-700">
                                                {{ $demanda->turno_preferencial_formatado }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Data Solicitação / Espera -->
                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs">
                                        <div class="font-bold text-slate-800">{{ $demanda->data_solicitacao->format('d/m/Y') }}</div>
                                        <span class="text-slate-500">{{ $demanda->created_at->diffForHumans() }}</span>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        @php
                                            $stClasses = [
                                                'aguardando' => 'bg-purple-100 text-purple-800 border-purple-200',
                                                'agendado' => 'bg-teal-100 text-teal-800 border-teal-200',
                                                'desistente' => 'bg-slate-100 text-slate-600 border-slate-200',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $stClasses[$demanda->status] ?? 'bg-slate-100 text-slate-700' }}">
                                            {{ ucfirst($demanda->status) }}
                                        </span>
                                    </td>

                                    <!-- Ação Regulatória -->
                                    <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($demanda->status === 'aguardando')
                                                <!-- Promover para Agendamento -->
                                                <button type="button" @click="abrirModalPromover({{ $demanda->id }}, '{{ addslashes($demanda->paciente->nome_completo) }}', '{{ addslashes($demanda->especialidade->nome) }}')" class="px-2.5 py-1 bg-teal-600 text-white rounded text-xs font-bold hover:bg-teal-700 transition flex items-center gap-1 shadow-xs">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    Agendar Vaga
                                                </button>

                                                <!-- Marcar Desistente -->
                                                <form method="POST" action="{{ route('demanda-reprimida.destroy', $demanda) }}" onsubmit="return confirm('Deseja marcar este paciente como desistente na fila?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded transition" title="Marcar como Desistente">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-slate-400 italic">Concluído</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                        Nenhum paciente aguardando na fila de espera com os filtros selecionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($demandas->hasPages())
                    <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                        {{ $demandas->links() }}
                    </div>
                @endif
            </div>

            <!-- MODAL ALPINE.JS: PROMOVER DA FILA PARA AGENDAMENTO (RF20) -->
            <div x-show="modalPromoverAberto" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                    <div x-show="modalPromoverAberto" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-slate-900/60 transition-opacity"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="modalPromoverAberto" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full p-4 sm:p-6 max-h-[90vh] overflow-y-auto">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div>
                                <h3 class="font-bold text-lg text-slate-900">Promover para Agendamento</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Vincule o paciente a uma vaga do profissional</p>
                            </div>
                            <button @click="modalPromoverAberto = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                        </div>

                        <div class="mt-3 p-3 bg-teal-50 border border-teal-200 rounded-lg text-xs space-y-1">
                            <div><span class="text-slate-500">Paciente:</span> <strong class="text-slate-900" x-text="pacienteNome"></strong></div>
                            <div><span class="text-slate-500">Especialidade:</span> <strong class="text-teal-800" x-text="especialidadeNome"></strong></div>
                        </div>

                        <form :action="actionUrl" method="POST" class="space-y-4 mt-4">
                            @csrf

                            <!-- Dentista -->
                            <div>
                                <label for="promover_dentista_id" class="block text-xs font-bold text-slate-700 uppercase mb-1">Dentista Responsável *</label>
                                <select name="dentista_id" id="promover_dentista_id" required class="w-full rounded-lg border-slate-300 text-sm focus:ring-teal-500 focus:border-teal-500">
                                    <option value="">Selecione o profissional...</option>
                                    @foreach($dentistas as $dent)
                                        <option value="{{ $dent->id }}">
                                            Dr(a). {{ $dent->nome_completo }} — {{ $dent->especialidade->nome ?? 'Geral' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Data Agendamento -->
                            <div>
                                <label for="promover_data_agendamento" class="block text-xs font-bold text-slate-700 uppercase mb-1">Data da Consulta *</label>
                                <input type="date" name="data_agendamento" id="promover_data_agendamento" value="{{ now()->toDateString() }}" min="{{ now()->toDateString() }}" required class="w-full rounded-lg border-slate-300 text-sm focus:ring-teal-500 focus:border-teal-500">
                            </div>

                            <!-- Turno -->
                            <div>
                                <label for="promover_turno" class="block text-xs font-bold text-slate-700 uppercase mb-1">Turno *</label>
                                <select name="turno" id="promover_turno" required class="w-full rounded-lg border-slate-300 text-sm focus:ring-teal-500 focus:border-teal-500">
                                    <option value="manha">Manhã (07h às 12h)</option>
                                    <option value="tarde">Tarde (13h às 18h)</option>
                                    <option value="noite">Noite (18h às 22h)</option>
                                </select>
                            </div>

                            <!-- Tipo -->
                            <div>
                                <label for="promover_tipo" class="block text-xs font-bold text-slate-700 uppercase mb-1">Tipo de Vaga</label>
                                <select name="tipo" id="promover_tipo" class="w-full rounded-lg border-slate-300 text-sm focus:ring-teal-500 focus:border-teal-500">
                                    <option value="normal">Consulta Regular (Vaga de Grade)</option>
                                    <option value="encaixe">Encaixe de Urgência</option>
                                </select>
                            </div>

                            <!-- Botões -->
                            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                                <button type="button" @click="modalPromoverAberto = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-xs font-bold hover:bg-slate-200 transition">
                                    Cancelar
                                </button>
                                <button type="submit" class="px-5 py-2 bg-teal-600 text-white rounded-lg text-xs font-bold hover:bg-teal-700 transition shadow-xs">
                                    Confirmar e Agendar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
