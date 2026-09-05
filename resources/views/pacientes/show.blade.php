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
                    Histórico clínico e agendamentos odontológicos do paciente
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('agenda.create', ['paciente_id' => $paciente->id]) }}" class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg font-semibold text-xs uppercase tracking-widest hover:bg-teal-700 transition shadow-xs">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Novo Agendamento
                </a>
                <a href="{{ route('demanda-reprimida.create', ['paciente_id' => $paciente->id]) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg font-semibold text-xs uppercase tracking-widest hover:bg-purple-700 transition shadow-xs">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Inserir na Fila
                </a>
                <a href="{{ route('pacientes.edit', $paciente) }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg font-semibold text-xs uppercase tracking-widest hover:bg-slate-50 transition shadow-xs">
                    Editar Dados
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Ficha Resumo do Paciente -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Dados Cadastrais e Encaminhamento</h3>
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
                        <div class="text-slate-800">
                            {{ $paciente->data_nascimento ? $paciente->data_nascimento->format('d/m/Y') . ' (' . $paciente->data_nascimento->age . ' anos)' : 'N/D' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400">UBS de Referência</div>
                        <div class="font-semibold text-teal-700">{{ $paciente->ubs->nome ?? 'Não vinculada' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400">Telefones</div>
                        <div class="text-slate-800 font-medium">
                            {{ $paciente->telefone_1 }}
                            @if($paciente->telefone_2)
                                <span class="text-xs text-slate-400 block">{{ $paciente->telefone_2 }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400">ACS Responsável</div>
                        <div class="text-slate-800">{{ $paciente->nome_acs ?? 'Não informado' }}</div>
                    </div>
                </div>
                @if($paciente->endereco)
                    <div class="mt-4 pt-4 border-t border-slate-100 text-xs text-slate-600">
                        <span class="font-bold text-slate-700">Endereço Residencial:</span> {{ $paciente->endereco }}
                    </div>
                @endif
            </div>

            <!-- Histórico de Agendamentos -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                    <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                        <span>Histórico de Agendamentos no CEO</span>
                        <span class="text-xs bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">{{ $paciente->agendamentos->count() }}</span>
                    </h3>
                </div>

                @if($paciente->agendamentos->isEmpty())
                    <div class="p-8 text-center text-slate-400 text-sm">
                        Nenhum agendamento realizado para este paciente.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3">Data / Turno</th>
                                    <th class="px-4 py-3">Dentista / Especialidade</th>
                                    <th class="px-4 py-3 text-center">Tipo</th>
                                    <th class="px-4 py-3 text-center">Chegada</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($paciente->agendamentos as $agendamento)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="font-bold text-slate-800">{{ $agendamento->data_agendamento->format('d/m/Y') }}</div>
                                            <span class="text-xs font-semibold uppercase text-slate-500">{{ $agendamento->turno }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-slate-800">{{ $agendamento->dentista->nome }}</div>
                                            <div class="text-xs text-teal-700">{{ $agendamento->especialidade->nome }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            @if($agendamento->tipo === 'encaixe')
                                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-rose-100 text-rose-800">Encaixe</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">Normal</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap font-mono text-xs">
                                            {{ $agendamento->horario_chegada ? \Carbon\Carbon::parse($agendamento->horario_chegada)->format('H:i') : '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $agendamento->status === 'concluido' ? 'bg-teal-100 text-teal-800' : ($agendamento->status === 'falta' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-800') }}">
                                                {{ ucfirst($agendamento->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Fila de Demanda Reprimida -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-200 flex items-center justify-between bg-purple-50/30">
                    <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                        <span>Fila de Espera (Demanda Reprimida)</span>
                        <span class="text-xs bg-purple-200 text-purple-800 px-2 py-0.5 rounded-full">{{ $paciente->demandasReprimidas->count() }}</span>
                    </h3>
                </div>

                @if($paciente->demandasReprimidas->isEmpty())
                    <div class="p-8 text-center text-slate-400 text-sm">
                        O paciente não possui solicitações ativas ou anteriores na fila de espera.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3">Solicitação</th>
                                    <th class="px-4 py-3">Especialidade</th>
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
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $demanda->prioridade === 'urgente' ? 'bg-rose-100 text-rose-800' : ($demanda->prioridade === 'alta' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">
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
    </div>
</x-app-layout>
