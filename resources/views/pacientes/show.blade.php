<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        {{ $paciente->nome_completo }}
                    </h2>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-teal-100 text-teal-800">
                        Prontuário #{{ str_pad((string)$paciente->id, 5, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-1">
                    Histórico clínico e prontuário odontológico municipal
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('agenda.create', ['paciente_id' => $paciente->id]) }}" class="inline-flex items-center px-3.5 py-2 bg-teal-600 text-white rounded-lg font-semibold text-xs uppercase tracking-wider hover:bg-teal-700 transition shadow-xs">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Novo Agendamento
                </a>
                <a href="{{ route('demanda-reprimida.create', ['paciente_id' => $paciente->id]) }}" class="inline-flex items-center px-3.5 py-2 bg-purple-600 text-white rounded-lg font-semibold text-xs uppercase tracking-wider hover:bg-purple-700 transition shadow-xs">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Inserir na Fila
                </a>
                <a href="{{ route('pacientes.edit', $paciente) }}" class="inline-flex items-center px-3.5 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg font-semibold text-xs uppercase tracking-wider hover:bg-slate-50 transition shadow-xs">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Editar
                </a>
                <button type="button" 
                        @click="$dispatch('abrir-modal-exclusao')"
                        class="inline-flex items-center px-3.5 py-2 bg-rose-50 border border-rose-200 text-rose-700 rounded-lg font-semibold text-xs uppercase tracking-wider hover:bg-rose-100 transition shadow-xs">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Excluir
                </button>
                <a href="{{ route('pacientes.index') }}" class="inline-flex items-center px-3 py-2 bg-slate-100 text-slate-600 rounded-lg font-semibold text-xs uppercase tracking-wider hover:bg-slate-200 transition">
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        deleteModalOpen: false
    }" @abrir-modal-exclusao.window="deleteModalOpen = true">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Mensagens de Flash -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg text-sm flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-lg text-sm flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Ficha Resumo do Paciente -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 space-y-6">
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">
                        Dados Cadastrais e Encaminhamento SUS
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 text-sm">
                        <div>
                            <div class="text-xs text-slate-400">CPF</div>
                            <div class="font-mono font-bold text-slate-800">{{ $paciente->cpf_formatado }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400">Cartão SUS (CNS)</div>
                            <div class="font-mono text-slate-800">{{ $paciente->cns ?? 'Não informado' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400">Nascimento / Idade</div>
                            <div class="text-slate-800 font-medium">
                                {{ $paciente->data_nascimento ? $paciente->data_nascimento->format('d/m/Y') . ' (' . $paciente->data_nascimento->age . ' anos)' : 'N/D' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400">Sexo</div>
                            <div class="text-slate-800 font-medium">
                                {{ $paciente->sexo === 'M' ? 'Masculino' : ($paciente->sexo === 'F' ? 'Feminino' : 'Outro') }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400">UBS de Referência</div>
                            <div class="font-semibold text-teal-700">{{ $paciente->ubs->nome ?? 'Não vinculada' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400">Agente Comunitário (ACS)</div>
                            <div class="text-slate-800 font-medium">{{ $paciente->nome_acs ?? 'Não informado' }}</div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">
                        Contatos e Endereço
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div>
                            <div class="text-xs text-slate-400">Telefone Principal (WhatsApp)</div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="font-semibold text-slate-800">{{ $paciente->telefone_1 }}</span>
                                @php
                                    $numsTel = preg_replace('/\D/', '', (string)$paciente->telefone_1);
                                @endphp
                                @if(strlen($numsTel) >= 10)
                                    <a href="https://wa.me/55{{ $numsTel }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-2 py-0.5 rounded border border-emerald-200 transition">
                                        WhatsApp
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400">Telefone Recado</div>
                            <div class="font-medium text-slate-800 mt-0.5">{{ $paciente->telefone_2 ?? 'Não informado' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400">Endereço Completo</div>
                            <div class="text-slate-800 mt-0.5">{{ $paciente->endereco ?: 'Endereço não cadastrado' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Histórico de Agendamentos -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                        <h3 class="font-bold text-slate-800 text-sm">Histórico de Atendimentos no CEO</h3>
                        <span class="text-xs bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full font-bold">
                            {{ $paciente->agendamentos->count() }}
                        </span>
                    </div>
                    <a href="{{ route('agenda.create', ['paciente_id' => $paciente->id]) }}" class="text-xs font-bold text-teal-700 hover:text-teal-800 transition flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Agendar Horário
                    </a>
                </div>

                @if($paciente->agendamentos->isEmpty())
                    <div class="p-8 text-center text-slate-400 text-sm">
                        Nenhum atendimento ou agendamento registrado até o momento para este paciente.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600 min-w-[700px]">
                            <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3">Data / Turno</th>
                                    <th class="px-4 py-3">Dentista Especialista</th>
                                    <th class="px-4 py-3">Especialidade</th>
                                    <th class="px-4 py-3 text-center">Tipo</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3">Chegada</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($paciente->agendamentos as $agendamento)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="font-bold text-slate-900">{{ $agendamento->data_agendamento->format('d/m/Y') }}</div>
                                            <span class="text-xs text-slate-400 capitalize">{{ $agendamento->turno }}</span>
                                        </td>
                                        <td class="px-4 py-3 font-medium text-slate-800 whitespace-nowrap">
                                            {{ $agendamento->dentista->nome }}
                                            <span class="text-xs text-slate-400 block font-mono">CRO: {{ $agendamento->dentista->cro }}</span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-teal-50 text-teal-800 border border-teal-100">
                                                {{ $agendamento->especialidade->nome }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $agendamento->tipo === 'encaixe' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}">
                                                {{ ucfirst($agendamento->tipo) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold 
                                                @if($agendamento->status === 'agendado') bg-blue-100 text-blue-800
                                                @elseif($agendamento->status === 'presente') bg-amber-100 text-amber-800
                                                @elseif($agendamento->status === 'em_atendimento') bg-purple-100 text-purple-800
                                                @elseif($agendamento->status === 'concluido') bg-emerald-100 text-emerald-800
                                                @elseif($agendamento->status === 'falta') bg-rose-100 text-rose-800
                                                @else bg-slate-100 text-slate-700 @endif">
                                                {{ ucfirst(str_replace('_', ' ', $agendamento->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-slate-500 whitespace-nowrap">
                                            {{ $agendamento->horario_chegada ? substr($agendamento->horario_chegada, 0, 5) : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Fila de Espera (Demanda Reprimida) -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                        <h3 class="font-bold text-slate-800 text-sm">Fila de Espera (Demanda Reprimida)</h3>
                        <span class="text-xs bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full font-bold">
                            {{ $paciente->demandasReprimidas->count() }}
                        </span>
                    </div>
                    <a href="{{ route('demanda-reprimida.create', ['paciente_id' => $paciente->id]) }}" class="text-xs font-bold text-purple-700 hover:text-purple-800 transition flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Inserir na Fila
                    </a>
                </div>

                @if($paciente->demandasReprimidas->isEmpty())
                    <div class="p-8 text-center text-slate-400 text-sm">
                        O paciente não possui solicitações pendentes na fila de espera.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600 min-w-[650px]">
                            <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3">Solicitação</th>
                                    <th class="px-4 py-3">Especialidade</th>
                                    <th class="px-4 py-3 text-center">Turno Preferencial</th>
                                    <th class="px-4 py-3 text-center">Prioridade</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3">Observações Clínicas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($paciente->demandasReprimidas as $demanda)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="font-bold text-slate-800">{{ $demanda->data_solicitacao->format('d/m/Y') }}</div>
                                            <span class="text-xs text-slate-400">{{ $demanda->created_at->diffForHumans() }}</span>
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-slate-800">
                                            {{ $demanda->especialidade->nome }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-xs text-slate-600 capitalize">
                                            {{ $demanda->turno_preferencial }}
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $demanda->prioridade === 'urgente' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700' }}">
                                                {{ ucfirst($demanda->prioridade) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $demanda->status === 'aguardando' ? 'bg-purple-100 text-purple-800' : ($demanda->status === 'agendado' ? 'bg-teal-100 text-teal-800' : 'bg-slate-100 text-slate-600') }}">
                                                {{ ucfirst($demanda->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-slate-600">
                                            {{ $demanda->observacoes ?: 'Sem observações' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>

        <!-- Modal de Confirmação de Exclusão -->
        <div x-show="deleteModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="deleteModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200"
                     @click.away="deleteModalOpen = false">
                    
                    <div class="bg-rose-50 px-6 py-4 border-b border-rose-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center text-rose-600 shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-rose-950">Excluir Paciente</h3>
                                <p class="text-xs text-rose-700">Remoção de cadastro no sistema</p>
                            </div>
                        </div>
                        <button type="button" @click="deleteModalOpen = false" class="text-rose-400 hover:text-rose-600 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        @if($paciente->agendamentos->count() > 0)
                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-900 leading-relaxed">
                                <div class="font-bold flex items-center gap-1.5 mb-1 text-amber-950">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Exclusão Bloqueada
                                </div>
                                Este paciente possui <strong>{{ $paciente->agendamentos->count() }}</strong> atendimento(s)/agendamento(s) registrado(s). Por conformidade legal e auditoria clínica do SUS, registros com histórico de agendamentos não podem ser excluídos.
                            </div>
                        @else
                            <div class="text-sm text-slate-600">
                                <p>Tem certeza que deseja excluir o cadastro de:</p>
                                <div class="mt-3 p-3 bg-slate-50 border border-slate-200 rounded-lg">
                                    <div class="font-bold text-slate-900">{{ $paciente->nome_completo }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">CPF: <span class="font-mono text-slate-700">{{ $paciente->cpf_formatado }}</span></div>
                                </div>
                                <p class="text-xs text-slate-400 mt-3">
                                    Esta ação é permanente e removerá os dados cadastrais do paciente.
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="bg-slate-50 px-6 py-3 border-t border-slate-200 flex justify-end gap-2">
                        <button type="button" 
                                @click="deleteModalOpen = false" 
                                class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                            Cancelar
                        </button>

                        @if($paciente->agendamentos->count() === 0)
                            <form action="{{ route('pacientes.destroy', $paciente) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Confirmar Exclusão</span>
                                </button>
                            </form>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
