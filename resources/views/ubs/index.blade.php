<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Unidades Básicas de Saúde (UBS)
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Rede municipal de atenção básica encaminhadora para o CEO
                </p>
            </div>
            <a href="{{ route('ubs.create') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 transition shadow-xs">
                <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nova UBS
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Search Card -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <form method="GET" action="{{ route('ubs.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="busca" value="{{ $busca ?? '' }}" placeholder="Pesquisar UBS por nome, endereço ou responsável..." class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="submit" class="flex-1 sm:flex-initial px-5 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-900 transition">
                            Buscar
                        </button>
                        @if($busca)
                            <a href="{{ route('ubs.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-200 transition text-center">
                                Limpar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 min-w-[700px]">
                        <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3.5">Nome da Unidade</th>
                                <th class="px-4 py-3.5">Endereço / Bairro</th>
                                <th class="px-4 py-3.5">Diretor / Responsável</th>
                                <th class="px-4 py-3.5">Contato</th>
                                <th class="px-4 py-3.5 text-center">Pacientes Vinculados</th>
                                <th class="px-4 py-3.5 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($unidades as $unidade)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-3.5">
                                        <a href="{{ route('ubs.show', $unidade) }}" class="font-bold text-slate-900 hover:text-teal-600 transition block">
                                            {{ $unidade->nome }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3.5 text-xs text-slate-600">
                                        {{ $unidade->endereco ?? 'Não cadastrado' }}
                                    </td>
                                    <td class="px-4 py-3.5 text-xs text-slate-800">
                                        {{ $unidade->diretor ?? 'Não informado' }}
                                    </td>
                                    <td class="px-4 py-3.5 text-xs text-slate-600 font-mono">
                                        {{ $unidade->contato ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        @if($unidade->pacientes_count > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                                {{ $unidade->pacientes_count }} paciente(s)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                                0 pacientes
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('ubs.show', $unidade) }}" class="p-1.5 text-slate-500 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition" title="Visualizar Detalhes">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <a href="{{ route('ubs.edit', $unidade) }}" class="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Editar UBS">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            
                                            <!-- Botão de Excluir -->
                                            @if($unidade->pacientes_count > 0)
                                                <button type="button" 
                                                        onclick="alert('A UBS \'{{ addslashes($unidade->nome) }}\' não pode ser excluída pois possui {{ $unidade->pacientes_count }} paciente(s) vinculado(s). Transfira ou desvincule os pacientes antes de remover esta unidade.')" 
                                                        class="p-1.5 text-slate-300 hover:text-slate-400 rounded-lg transition cursor-not-allowed" 
                                                        title="Não é possível excluir: existem {{ $unidade->pacientes_count }} paciente(s) vinculados">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            @else
                                                <form method="POST" action="{{ route('ubs.destroy', $unidade) }}" onsubmit="return confirm('Deseja realmente excluir a UBS \'{{ addslashes($unidade->nome) }}\'? Esta ação é permanente e não poderá ser desfeita.')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition" title="Excluir UBS">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                        Nenhuma Unidade Básica de Saúde encontrada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($unidades->hasPages())
                    <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                        {{ $unidades->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
