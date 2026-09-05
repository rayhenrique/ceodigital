<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Cadastrar Nova UBS
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Adicionar unidade básica de saúde da rede de atenção primária
                </p>
            </div>
            <a href="{{ route('ubs.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-50 transition shadow-xs">
                Voltar à Lista
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 sm:p-8">
                <form method="POST" action="{{ route('ubs.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="nome" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nome da Unidade *</label>
                        <input type="text" id="nome" name="nome" value="{{ old('nome') }}" required placeholder="Ex: UBS Dr. José Silva" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                        @error('nome') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="endereco" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Endereço / Bairro</label>
                        <input type="text" id="endereco" name="endereco" value="{{ old('endereco') }}" placeholder="Ex: Av. Principal, 450 - Centro" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                        @error('endereco') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="diretor" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Diretor(a) / Coordenador(a)</label>
                        <input type="text" id="diretor" name="diretor" value="{{ old('diretor') }}" placeholder="Nome do responsável técnico ou diretor" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                        @error('diretor') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="contato" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Telefone / Ramal / E-mail</label>
                        <input type="text" id="contato" name="contato" value="{{ old('contato') }}" placeholder="(82) 3333-0000 / ubs@saude.gov.br" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                        @error('contato') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('ubs.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-200 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-teal-600 text-white text-sm font-bold rounded-lg hover:bg-teal-700 transition shadow-xs">
                            Cadastrar UBS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
