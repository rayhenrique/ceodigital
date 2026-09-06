<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Cadastro Geral de Pacientes
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Prontuários odontológicos e identificação única SUS no município
                </p>
            </div>
            <a href="{{ route('pacientes.create') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 transition shadow-xs self-start sm:self-auto">
                <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Novo Paciente
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        deleteModalOpen: false,
        pacienteParaExcluir: { id: null, nome: '', cpf: '', agendamentos: 0, demandas: 0 },
        abrirExclusao(id, nome, cpf, agendamentos, demandas) {
            this.pacienteParaExcluir = { id, nome, cpf, agendamentos, demandas };
            this.deleteModalOpen = true;
        }
    }">
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

            <!-- Search and Filter Card -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200 shadow-xs">
                <form method="GET" action="{{ route('pacientes.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <div class="sm:col-span-7 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" 
                               name="busca" 
                               value="{{ $busca ?? '' }}" 
                               placeholder="Pesquisar por Nome Completo, CPF ou Cartão SUS (CNS)..." 
                               class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
                    </div>

                    <div class="sm:col-span-3">
                        <select name="ubs_id" class="w-full py-2 border border-slate-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
                            <option value="">Todas as UBS de Referência</option>
                            @foreach($ubsList as $u)
                                <option value="{{ $u->id }}" {{ ($ubsId ?? '') == $u->id ? 'selected' : '' }}>
                                    {{ $u->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2 flex gap-2">
                        <button type="submit" class="flex-1 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-900 transition flex items-center justify-center gap-1">
                            <span>Filtrar</span>
                        </button>
                        @if($busca || $ubsId)
                            <a href="{{ route('pacientes.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-200 transition flex items-center justify-center" title="Limpar Filtros">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between text-xs text-slate-500 gap-2 bg-slate-50/50">
                    <div>
                        Exibindo <span class="font-bold text-slate-800">{{ $pacientes->firstItem() ?? 0 }}</span> a <span class="font-bold text-slate-800">{{ $pacientes->lastItem() ?? 0 }}</span> de <span class="font-bold text-slate-800">{{ $pacientes->total() }}</span> pacientes cadastrados
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 min-w-[750px]">
                        <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3.5">Nome / Idade</th>
                                <th class="px-4 py-3.5">Documentos (CPF / CNS)</th>
                                <th class="px-4 py-3.5">UBS Origem / ACS</th>
                                <th class="px-4 py-3.5">Contatos</th>
                                <th class="px-4 py-3.5 text-center">Histórico</th>
                                <th class="px-4 py-3.5 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($pacientes as $paciente)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-3.5">
                                        <a href="{{ route('pacientes.show', $paciente) }}" class="font-bold text-slate-900 hover:text-teal-600 transition block">
                                            {{ $paciente->nome_completo }}
                                        </a>
                                        <span class="text-xs text-slate-500">
                                            {{ $paciente->data_nascimento ? $paciente->data_nascimento->format('d/m/Y') . ' (' . $paciente->data_nascimento->age . ' anos)' : 'Data nasc. não inf.' }}
                                            @if($paciente->sexo)
                                                &bull; {{ $paciente->sexo === 'M' ? 'Masc' : ($paciente->sexo === 'F' ? 'Fem' : 'Outro') }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 font-mono text-xs">
                                        <div><span class="text-slate-400 font-sans">CPF:</span> <strong class="text-slate-800">{{ $paciente->cpf_formatado }}</strong></div>
                                        @if($paciente->cns)
                                            <div class="mt-0.5"><span class="text-slate-400 font-sans">CNS:</span> {{ $paciente->cns }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-teal-50 text-teal-800 border border-teal-100">
                                            {{ $paciente->ubs->nome ?? 'Sem UBS' }}
                                        </span>
                                        @if($paciente->nome_acs)
                                            <div class="text-[11px] text-slate-400 mt-0.5">ACS: {{ $paciente->nome_acs }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-xs">
                                        <div class="font-medium text-slate-800">{{ $paciente->telefone_1 ?? 'Sem telefone' }}</div>
                                        @if($paciente->telefone_2)
                                            <div class="text-slate-400 mt-0.5">{{ $paciente->telefone_2 }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1.5">
                                            @if($paciente->agendamentos_count > 0)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-teal-100 text-teal-800" title="{{ $paciente->agendamentos_count }} agendamento(s)">
                                                    {{ $paciente->agendamentos_count }} agend.
                                                </span>
                                            @else
                                                <span class="text-slate-300 text-xs">-</span>
                                            @endif
                                            @if($paciente->demandas_reprimidas_count > 0)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-purple-100 text-purple-800" title="{{ $paciente->demandas_reprimidas_count }} na fila">
                                                    {{ $paciente->demandas_reprimidas_count }} fila
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('agenda.create', ['paciente_id' => $paciente->id]) }}" 
                                               class="px-2.5 py-1 bg-teal-50 text-teal-700 hover:bg-teal-100 rounded-md text-xs font-bold transition inline-flex items-center gap-1" 
                                               title="Agendar Consulta">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                Agendar
                                            </a>
                                            <a href="{{ route('pacientes.show', $paciente) }}" 
                                               class="p-1.5 text-slate-500 hover:text-teal-600 hover:bg-slate-100 rounded-md transition" 
                                               title="Ver Prontuário">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <a href="{{ route('pacientes.edit', $paciente) }}" 
                                               class="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-slate-100 rounded-md transition" 
                                               title="Editar Paciente">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <button type="button" 
                                                    @click="abrirExclusao({{ $paciente->id }}, '{{ addslashes($paciente->nome_completo) }}', '{{ $paciente->cpf_formatado }}', {{ $paciente->agendamentos_count }}, {{ $paciente->demandas_reprimidas_count }})"
                                                    class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition" 
                                                    title="Excluir Paciente">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                            <p class="font-medium text-slate-600">Nenhum paciente encontrado.</p>
                                            <p class="text-xs text-slate-400">Tente ajustar os critérios de pesquisa ou cadastre um novo paciente.</p>
                                            <a href="{{ route('pacientes.create') }}" class="mt-2 px-3 py-1.5 bg-teal-600 text-white rounded-lg text-xs font-bold hover:bg-teal-700 transition shadow-xs">
                                                Cadastrar Novo Paciente
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($pacientes->hasPages())
                    <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                        {{ $pacientes->links() }}
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
                        <template x-if="pacienteParaExcluir.agendamentos > 0">
                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-900 leading-relaxed">
                                <div class="font-bold flex items-center gap-1.5 mb-1 text-amber-950">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Exclusão Bloqueada
                                </div>
                                Este paciente possui <strong x-text="pacienteParaExcluir.agendamentos"></strong> agendamento(s) registrado(s). Por conformidade legal e auditoria clínica do SUS, pacientes com histórico de agendamentos não podem ser excluídos.
                            </div>
                        </template>

                        <template x-if="pacienteParaExcluir.agendamentos === 0">
                            <div class="text-sm text-slate-600">
                                <p>Tem certeza que deseja excluir o cadastro do paciente:</p>
                                <div class="mt-3 p-3 bg-slate-50 border border-slate-200 rounded-lg">
                                    <div class="font-bold text-slate-900" x-text="pacienteParaExcluir.nome"></div>
                                    <div class="text-xs text-slate-500 mt-0.5">CPF: <span class="font-mono text-slate-700" x-text="pacienteParaExcluir.cpf"></span></div>
                                </div>
                                <p class="text-xs text-slate-400 mt-3">
                                    Esta ação é permanente e removerá os dados cadastrais do paciente.
                                </p>
                            </div>
                        </template>
                    </div>

                    <div class="bg-slate-50 px-6 py-3 border-t border-slate-200 flex justify-end gap-2">
                        <button type="button" 
                                @click="deleteModalOpen = false" 
                                class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                            Cancelar
                        </button>

                        <template x-if="pacienteParaExcluir.agendamentos === 0">
                            <form :action="'{{ url('pacientes') }}/' + pacienteParaExcluir.id" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Confirmar Exclusão</span>
                                </button>
                            </form>
                        </template>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
