<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Nova Especialidade Odontológica
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Cadastre uma nova especialidade reconhecida pelo CEO
                </p>
            </div>
            <a href="{{ route('especialidades.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-50 transition shadow-xs self-start sm:self-auto">
                Voltar à Lista
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 sm:p-8">
                <form method="POST" action="{{ route('especialidades.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="nome" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nome da Especialidade *</label>
                        <input type="text" id="nome" name="nome" value="{{ old('nome') }}" required placeholder="Ex: Endodontia, Periodontia, Cirurgia Oral Menor" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                        @error('nome') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="descricao" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Descrição dos Procedimentos Abrangidos</label>
                        <textarea id="descricao" name="descricao" rows="4" placeholder="Descreva os tratamentos e critérios de encaminhamento da atenção primária..." class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">{{ old('descricao') }}</textarea>
                        @error('descricao') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="status_ativo" name="status_ativo" value="1" {{ old('status_ativo', true) ? 'checked' : '' }} class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                        <label for="status_ativo" class="text-sm font-semibold text-slate-700">Especialidade Ativa no Sistema</label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('especialidades.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-200 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-teal-600 text-white text-sm font-bold rounded-lg hover:bg-teal-700 transition shadow-xs">
                            Cadastrar Especialidade
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
