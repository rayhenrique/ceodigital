<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Cadastro Geral de Pacientes
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Prontuários e identificação única SUS no município
                </p>
            </div>
            <a href="{{ route('pacientes.create') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 transition shadow-xs">
                <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Novo Paciente
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Search Filter Card -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <form method="GET" action="{{ route('pacientes.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="busca" value="{{ $busca ?? '' }}" placeholder="Pesquisar por Nome Completo, CPF ou Cartão SUS (CNS)..." class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-5 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-900 transition">
                            Pesquisar
                        </button>
                        @if($busca)
                            <a href="{{ route('pacientes.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-200 transition">
                                Limpar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3.5">Nome / Idade</th>
                                <th class="px-4 py-3.5">Documentos (CPF / CNS)</th>
                                <th class="px-4 py-3.5">UBS Origem</th>
                                <th class="px-4 py-3.5">Contatos</th>
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
                                        <div><span class="text-slate-400 font-sans">CPF:</span> <span class="font-bold text-slate-800">{{ $paciente->cpf_formatado }}</span></div>
                                        @if($paciente->cns)
                                            <div><span class="text-slate-400 font-sans">CNS:</span> {{ $paciente->cns }}</div>
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
                                        <div>{{ $paciente->telefone_1 ?? 'Sem telefone' }}</div>
                                        @if($paciente->telefone_2)
                                            <div class="text-slate-400">{{ $paciente->telefone_2 }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('agenda.create', ['paciente_id' => $paciente->id]) }}" class="px-2 py-1 bg-teal-50 text-teal-700 hover:bg-teal-100 rounded text-xs font-bold transition flex items-center gap-1" title="Novo Agendamento">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                Agendar
                                            </a>
                                            <a href="{{ route('pacientes.show', $paciente) }}" class="p-1.5 text-slate-500 hover:text-slate-800 rounded transition" title="Ver Prontuário">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <a href="{{ route('pacientes.edit', $paciente) }}" class="p-1.5 text-slate-500 hover:text-slate-800 rounded transition" title="Editar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                        Nenhum paciente encontrado com o critério informado.
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
    </div>
</x-app-layout>
