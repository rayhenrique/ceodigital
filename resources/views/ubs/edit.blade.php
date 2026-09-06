<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Editar Dados da UBS
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $ubs->nome }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('ubs.show', $ubs) }}" class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-700 rounded-lg font-semibold text-xs uppercase tracking-widest hover:bg-slate-200 transition shadow-xs">
                    Ver Detalhes
                </a>
                <a href="{{ route('ubs.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-50 transition shadow-xs">
                    Voltar à Lista
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 sm:p-8">
                <form method="POST" action="{{ route('ubs.update', $ubs) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="nome" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nome da Unidade *</label>
                        <input type="text" id="nome" name="nome" value="{{ old('nome', $ubs->nome) }}" required class="w-full rounded-xl border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                        @error('nome') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="endereco" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Endereço / Bairro</label>
                        <input type="text" id="endereco" name="endereco" value="{{ old('endereco', $ubs->endereco) }}" class="w-full rounded-xl border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                        @error('endereco') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="diretor" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Diretor(a) / Coordenador(a)</label>
                        <input type="text" id="diretor" name="diretor" value="{{ old('diretor', $ubs->diretor) }}" class="w-full rounded-xl border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                        @error('diretor') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="contato" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Telefone / Ramal / E-mail</label>
                        <input type="text" id="contato" name="contato" value="{{ old('contato', $ubs->contato) }}" class="w-full rounded-xl border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                        @error('contato') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('ubs.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-teal-600 text-white text-sm font-bold rounded-xl hover:bg-teal-700 transition shadow-xs">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>

            <!-- Zona de Perigo / Exclusão -->
            <div class="bg-white rounded-xl border border-rose-100 p-6 shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-bold text-rose-900">Excluir Unidade Básica de Saúde</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            @if($ubs->pacientes_count > 0)
                                Esta unidade possui <strong class="text-rose-600">{{ $ubs->pacientes_count }} paciente(s)</strong> vinculados e não pode ser removida no momento.
                            @else
                                Esta UBS não possui pacientes vinculados e pode ser excluída permanentemente.
                            @endif
                        </p>
                    </div>
                    @if($ubs->pacientes_count > 0)
                        <button type="button" 
                                onclick="alert('Não é possível excluir esta UBS pois existem {{ $ubs->pacientes_count }} paciente(s) vinculados a ela.')" 
                                class="px-4 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl cursor-not-allowed">
                            Excluir UBS
                        </button>
                    @else
                        <form method="POST" action="{{ route('ubs.destroy', $ubs) }}" onsubmit="return confirm('Deseja realmente excluir permanentemente a UBS \'{{ addslashes($ubs->nome) }}\'? Esta ação é irreversível.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition shadow-xs">
                                Excluir UBS
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
