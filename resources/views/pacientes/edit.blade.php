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
            <a href="{{ route('pacientes.show', $paciente) }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-50 transition shadow-xs self-start sm:self-auto">
                Voltar ao Prontuário
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 sm:p-8">
                <form method="POST" action="{{ route('pacientes.update', $paciente) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Dados Pessoais -->
                    <div>
                        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">
                            1. Identificação do Cidadão
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Nome Completo -->
                            <div class="sm:col-span-2">
                                <label for="nome_completo" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nome Completo *</label>
                                <input type="text" id="nome_completo" name="nome_completo" value="{{ old('nome_completo', $paciente->nome_completo) }}" required class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                                @error('nome_completo') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- CPF -->
                            <div>
                                <label for="cpf" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">CPF (11 dígitos) *</label>
                                <input type="text" id="cpf" name="cpf" value="{{ old('cpf', $paciente->cpf_formatado) }}" required maxlength="14" class="w-full rounded-lg border-slate-300 text-sm font-mono focus:border-teal-500 focus:ring-teal-500">
                                @error('cpf') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- CNS -->
                            <div>
                                <label for="cns" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Cartão Nacional de Saúde (CNS)</label>
                                <input type="text" id="cns" name="cns" value="{{ old('cns', $paciente->cns) }}" maxlength="15" class="w-full rounded-lg border-slate-300 text-sm font-mono focus:border-teal-500 focus:ring-teal-500">
                                @error('cns') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Data Nascimento -->
                            <div>
                                <label for="data_nascimento" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Data de Nascimento *</label>
                                <input type="date" id="data_nascimento" name="data_nascimento" value="{{ old('data_nascimento', $paciente->data_nascimento?->format('Y-m-d')) }}" required class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                                @error('data_nascimento') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Sexo -->
                            <div>
                                <label for="sexo" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Sexo *</label>
                                <select id="sexo" name="sexo" required class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                                    <option value="F" {{ old('sexo', $paciente->sexo) === 'F' ? 'selected' : '' }}>Feminino</option>
                                    <option value="M" {{ old('sexo', $paciente->sexo) === 'M' ? 'selected' : '' }}>Masculino</option>
                                    <option value="Outro" {{ old('sexo', $paciente->sexo) === 'Outro' ? 'selected' : '' }}>Outro</option>
                                </select>
                                @error('sexo') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Vinculação SUS & Agente Comunitário -->
                    <div>
                        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">
                            2. Vinculação na Atenção Básica (SUS)
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="ubs_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">UBS de Referência *</label>
                                <select id="ubs_id" name="ubs_id" required class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                                    @foreach($ubsList as $ubs)
                                        <option value="{{ $ubs->id }}" {{ old('ubs_id', $paciente->ubs_id) == $ubs->id ? 'selected' : '' }}>
                                            {{ $ubs->nome }} (CNES: {{ $ubs->cnes ?? 'N/D' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('ubs_id') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="nome_acs" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nome do Agente de Saúde (ACS)</label>
                                <input type="text" id="nome_acs" name="nome_acs" value="{{ old('nome_acs', $paciente->nome_acs) }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                                @error('nome_acs') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Contato e Endereço -->
                    <div>
                        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">
                            3. Contato e Endereço
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="telefone_1" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Telefone / WhatsApp Principal *</label>
                                <input type="text" id="telefone_1" name="telefone_1" value="{{ old('telefone_1', $paciente->telefone_1) }}" required class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                                @error('telefone_1') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="telefone_2" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Telefone Recado / Parente</label>
                                <input type="text" id="telefone_2" name="telefone_2" value="{{ old('telefone_2', $paciente->telefone_2) }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                                @error('telefone_2') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="endereco" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Endereço Residencial (Rua, Nº, Bairro)</label>
                                <input type="text" id="endereco" name="endereco" value="{{ old('endereco', $paciente->endereco) }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                                @error('endereco') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('pacientes.show', $paciente) }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-200 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-teal-600 text-white text-sm font-bold rounded-lg hover:bg-teal-700 transition shadow-xs">
                            Atualizar Paciente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
