<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Editar Dados do Paciente
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Atualização cadastral de {{ $paciente->nome_completo }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('pacientes.show', $paciente) }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-50 transition shadow-xs self-start sm:self-auto">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Ver Prontuário
                </a>
                <a href="{{ route('pacientes.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 rounded-lg font-semibold text-xs text-slate-600 uppercase tracking-widest hover:bg-slate-200 transition shadow-xs">
                    Lista
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        cpfVal: '{{ old('cpf', $paciente->cpf_formatado) }}',
        cnsVal: '{{ old('cns', $paciente->cns) }}',
        tel1Val: '{{ old('telefone_1', $paciente->telefone_1) }}',
        tel2Val: '{{ old('telefone_2', $paciente->telefone_2) }}',
        mascararCpf(e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 11) v = v.slice(0, 11);
            if (v.length > 9) v = v.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
            else if (v.length > 6) v = v.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
            else if (v.length > 3) v = v.replace(/(\d{3})(\d{1,3})/, '$1.$2');
            this.cpfVal = v;
        },
        mascararCns(e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 15) v = v.slice(0, 15);
            this.cnsVal = v;
        },
        mascararTelefone(e, prop) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 11) v = v.slice(0, 11);
            if (v.length > 10) {
                v = v.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            } else if (v.length > 5) {
                v = v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
            } else if (v.length > 2) {
                v = v.replace(/(\d{2})(\d{0,5})/, '($1) $2');
            }
            this[prop] = v;
        }
    }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-lg text-sm shadow-xs space-y-1">
                    <div class="font-bold flex items-center gap-1.5">
                        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Por favor, corrija os erros abaixo:
                    </div>
                    <ul class="list-disc list-inside pl-1 text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 sm:p-8">
                <form method="POST" action="{{ route('pacientes.update', $paciente) }}" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <!-- 1. Identificação do Cidadão -->
                    <div>
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2 mb-4">
                            <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-700 text-xs font-bold flex items-center justify-center">1</span>
                            <h3 class="text-base font-bold text-slate-800">Identificação do Cidadão</h3>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Nome Completo -->
                            <div class="sm:col-span-2">
                                <label for="nome_completo" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Nome Completo <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       id="nome_completo" 
                                       name="nome_completo" 
                                       value="{{ old('nome_completo', $paciente->nome_completo) }}" 
                                       required 
                                       class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500 @error('nome_completo') border-rose-400 bg-rose-50/50 @enderror">
                                @error('nome_completo') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- CPF -->
                            <div>
                                <label for="cpf" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    CPF (11 dígitos) <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       id="cpf" 
                                       name="cpf" 
                                       x-model="cpfVal"
                                       @input="mascararCpf($event)"
                                       required 
                                       maxlength="14" 
                                       class="w-full rounded-lg border-slate-300 text-sm font-mono focus:border-teal-500 focus:ring-teal-500 @error('cpf') border-rose-400 bg-rose-50/50 @enderror">
                                @error('cpf') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- CNS (Cartão SUS) -->
                            <div>
                                <label for="cns" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Cartão Nacional de Saúde (CNS)
                                </label>
                                <input type="text" 
                                       id="cns" 
                                       name="cns" 
                                       x-model="cnsVal"
                                       @input="mascararCns($event)"
                                       maxlength="15" 
                                       placeholder="Ex: 700000000000000" 
                                       class="w-full rounded-lg border-slate-300 text-sm font-mono focus:border-teal-500 focus:ring-teal-500 @error('cns') border-rose-400 bg-rose-50/50 @enderror">
                                @error('cns') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Data Nascimento -->
                            <div>
                                <label for="data_nascimento" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Data de Nascimento <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" 
                                       id="data_nascimento" 
                                       name="data_nascimento" 
                                       value="{{ old('data_nascimento', $paciente->data_nascimento?->format('Y-m-d')) }}" 
                                       max="{{ now()->format('Y-m-d') }}"
                                       required 
                                       class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500 @error('data_nascimento') border-rose-400 bg-rose-50/50 @enderror">
                                @error('data_nascimento') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Sexo -->
                            <div>
                                <label for="sexo" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Sexo <span class="text-rose-500">*</span>
                                </label>
                                <select id="sexo" 
                                        name="sexo" 
                                        required 
                                        class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500 @error('sexo') border-rose-400 bg-rose-50/50 @enderror">
                                    <option value="F" {{ old('sexo', $paciente->sexo) === 'F' ? 'selected' : '' }}>Feminino</option>
                                    <option value="M" {{ old('sexo', $paciente->sexo) === 'M' ? 'selected' : '' }}>Masculino</option>
                                    <option value="Outro" {{ old('sexo', $paciente->sexo) === 'Outro' ? 'selected' : '' }}>Outro</option>
                                </select>
                                @error('sexo') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- 2. Vínculo com a Atenção Primária -->
                    <div>
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2 mb-4">
                            <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-700 text-xs font-bold flex items-center justify-center">2</span>
                            <h3 class="text-base font-bold text-slate-800">Vínculo com a Atenção Primária</h3>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- UBS de Origem -->
                            <div>
                                <label for="ubs_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    UBS de Referência <span class="text-rose-500">*</span>
                                </label>
                                <select id="ubs_id" 
                                        name="ubs_id" 
                                        required 
                                        class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500 @error('ubs_id') border-rose-400 bg-rose-50/50 @enderror">
                                    @foreach($ubsList as $ubs)
                                        <option value="{{ $ubs->id }}" {{ old('ubs_id', $paciente->ubs_id) == $ubs->id ? 'selected' : '' }}>
                                            {{ $ubs->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('ubs_id') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Nome ACS -->
                            <div>
                                <label for="nome_acs" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Agente Comunitário de Saúde (ACS)
                                </label>
                                <input type="text" 
                                       id="nome_acs" 
                                       name="nome_acs" 
                                       value="{{ old('nome_acs', $paciente->nome_acs) }}" 
                                       placeholder="Ex: ACS Maria Pereira" 
                                       class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500 @error('nome_acs') border-rose-400 bg-rose-50/50 @enderror">
                                @error('nome_acs') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- 3. Contatos e Endereço -->
                    <div>
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2 mb-4">
                            <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-700 text-xs font-bold flex items-center justify-center">3</span>
                            <h3 class="text-base font-bold text-slate-800">Contatos e Endereço</h3>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Telefone 1 -->
                            <div>
                                <label for="telefone_1" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Telefone Principal (WhatsApp / Celular) <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       id="telefone_1" 
                                       name="telefone_1" 
                                       x-model="tel1Val"
                                       @input="mascararTelefone($event, 'tel1Val')"
                                       required 
                                       class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500 @error('telefone_1') border-rose-400 bg-rose-50/50 @enderror">
                                @error('telefone_1') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Telefone 2 -->
                            <div>
                                <label for="telefone_2" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Telefone Secundário / Recado
                                </label>
                                <input type="text" 
                                       id="telefone_2" 
                                       name="telefone_2" 
                                       x-model="tel2Val"
                                       @input="mascararTelefone($event, 'tel2Val')"
                                       class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500 @error('telefone_2') border-rose-400 bg-rose-50/50 @enderror">
                                @error('telefone_2') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Endereço -->
                            <div class="sm:col-span-2">
                                <label for="endereco" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Endereço Residencial Completo
                                </label>
                                <textarea id="endereco" 
                                          name="endereco" 
                                          rows="2" 
                                          class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500 @error('endereco') border-rose-400 bg-rose-50/50 @enderror">{{ old('endereco', $paciente->endereco) }}</textarea>
                                @error('endereco') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('pacientes.show', $paciente) }}" class="px-5 py-2.5 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-700 uppercase tracking-wider hover:bg-slate-50 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-teal-600 border border-transparent rounded-lg text-xs font-bold text-white uppercase tracking-wider hover:bg-teal-700 transition shadow-xs flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Atualizar Paciente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
