<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Editar Especialidade Odontológica
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $especialidade->nome }}
                </p>
            </div>
            <a href="{{ route('especialidades.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-50 transition shadow-xs self-start sm:self-auto">
                Voltar à Lista
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 sm:p-8">
                <form method="POST" action="{{ route('especialidades.update', $especialidade) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="nome" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nome da Especialidade *</label>
                        <input type="text" id="nome" name="nome" value="{{ old('nome', $especialidade->nome) }}" required class="w-full rounded-xl border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                        @error('nome') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="descricao" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Descrição dos Procedimentos Abrangidos</label>
                        <textarea id="descricao" name="descricao" rows="4" class="w-full rounded-xl border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">{{ old('descricao', $especialidade->descricao) }}</textarea>
                        @error('descricao') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="status_ativo" name="status_ativo" value="1" {{ old('status_ativo', $especialidade->status_ativo) ? 'checked' : '' }} class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                        <label for="status_ativo" class="text-sm font-semibold text-slate-700">Especialidade Ativa no Sistema</label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('especialidades.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-teal-600 text-white text-sm font-bold rounded-xl hover:bg-teal-700 transition shadow-xs">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>

            <!-- Zona de Perigo / Exclusão -->
            @if(Auth::user()->isAdmin())
                @php
                    $temPacientes = ($especialidade->agendamentos_count > 0 || $especialidade->demandas_reprimidas_count > 0);
                    $temDentistas = ($especialidade->dentistas_count > 0);
                    $podeExcluir = (!$temPacientes && !$temDentistas);

                    $motivoBloqueio = [];
                    if ($especialidade->agendamentos_count > 0) $motivoBloqueio[] = "{$especialidade->agendamentos_count} agendamento(s) de pacientes";
                    if ($especialidade->demandas_reprimidas_count > 0) $motivoBloqueio[] = "{$especialidade->demandas_reprimidas_count} paciente(s) na fila de espera";
                    if ($especialidade->dentistas_count > 0) $motivoBloqueio[] = "{$especialidade->dentistas_count} dentista(s) vinculado(s)";
                    $textoBloqueio = implode(', ', $motivoBloqueio);
                @endphp

                <div class="bg-white rounded-xl border border-rose-100 p-6 shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-bold text-rose-900">Excluir Especialidade Odontológica</h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                @if(!$podeExcluir)
                                    Esta especialidade possui vínculos ativos (<strong class="text-rose-600">{{ $textoBloqueio }}</strong>) e não pode ser removida no momento.
                                @else
                                    Esta especialidade não possui pacientes vinculados nem profissionais e pode ser excluída permanentemente.
                                @endif
                            </p>
                        </div>
                        @if(!$podeExcluir)
                            <button type="button" 
                                    onclick="alert('Não é possível excluir esta especialidade pois há vínculos ativos: {{ $textoBloqueio }}.')" 
                                    class="px-4 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl cursor-not-allowed">
                                Excluir Especialidade
                            </button>
                        @else
                            <form method="POST" action="{{ route('especialidades.destroy', $especialidade) }}" onsubmit="return confirm('Deseja realmente excluir permanentemente a especialidade \'{{ addslashes($especialidade->nome) }}\'? Esta ação é irreversível.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition shadow-xs">
                                    Excluir Especialidade
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
