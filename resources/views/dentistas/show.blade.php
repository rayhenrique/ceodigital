<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        Dr(a). {{ $dentista->nome_completo }}
                    </h2>
                    @if($dentista->status_ativo)
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                            Ativo
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-600">
                            Inativo
                        </span>
                    @endif
                </div>
                <p class="text-sm text-slate-500 mt-1 font-mono">
                    CRO: {{ $dentista->cro }} &bull; Especialidade: {{ $dentista->especialidade->nome ?? 'Clínico Geral' }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('agenda.index', ['dentista_id' => $dentista->id]) }}" class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg font-semibold text-xs uppercase tracking-widest hover:bg-teal-700 transition shadow-xs">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Ver na Agenda
                </a>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('dentistas.edit', $dentista) }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg font-semibold text-xs uppercase tracking-widest hover:bg-slate-50 transition shadow-xs">
                        Editar
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Resumo e Escala -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Informações Cadastrais -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Informações do Profissional</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-xs text-slate-400 block">CRO</span>
                            <span class="font-mono font-bold text-slate-800">{{ $dentista->cro }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block">Especialidade</span>
                            <span class="font-bold text-teal-700">{{ $dentista->especialidade->nome ?? 'Geral' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block">Telefone</span>
                            <span class="text-slate-800">{{ $dentista->telefone ?: 'Não informado' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Grade Semanal de Atendimento -->
                <div class="md:col-span-2 bg-white rounded-xl border border-slate-200 shadow-xs p-6">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Escala de Turnos Semanal</h3>
                    @if($dentista->grades->isEmpty())
                        <p class="text-sm text-slate-400 italic">Nenhuma grade cadastrada.</p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($dentista->grades as $grade)
                                <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg">
                                    <div class="font-bold text-slate-800 text-sm">{{ $grade->dia_semana_texto }}</div>
                                    <div class="flex items-center justify-between mt-1 text-xs">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded font-semibold {{ $grade->turno === 'manha' ? 'bg-amber-100 text-amber-800' : ($grade->turno === 'tarde' ? 'bg-orange-100 text-orange-800' : 'bg-indigo-100 text-indigo-800') }}">
                                            Turno {{ ucfirst($grade->turno) }}
                                        </span>
                                        <span class="font-bold text-slate-600">{{ $grade->vagas_padrao }} vagas</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Últimos Atendimentos -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                    <h3 class="font-bold text-base text-slate-900">
                        Últimos Atendimentos Realizados
                    </h3>
                </div>

                @if($dentista->agendamentos->isEmpty())
                    <div class="p-8 text-center text-slate-400 text-sm">
                        Nenhum atendimento registrado para este profissional ainda.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600 min-w-[550px]">
                            <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3">Data / Turno</th>
                                    <th class="px-4 py-3">Paciente</th>
                                    <th class="px-4 py-3 text-center">Tipo</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($dentista->agendamentos as $agendamento)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-3 font-medium text-slate-800 whitespace-nowrap">
                                            {{ $agendamento->data_agendamento->format('d/m/Y') }} &bull; {{ ucfirst($agendamento->turno) }}
                                        </td>
                                        <td class="px-4 py-3 font-bold text-slate-900">
                                            <a href="{{ route('pacientes.show', $agendamento->paciente) }}" class="hover:text-teal-600">
                                                {{ $agendamento->paciente->nome_completo }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $agendamento->tipo === 'encaixe' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700' }}">
                                                {{ ucfirst($agendamento->tipo) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $agendamento->status === 'concluido' ? 'bg-teal-100 text-teal-800' : ($agendamento->status === 'falta' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-800') }}">
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

        </div>
    </div>
</x-app-layout>
