<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Inserir Paciente na Fila de Espera
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Demanda reprimida regulada para especialidades sem vagas imediatas
                </p>
            </div>
            <a href="{{ route('demanda-reprimida.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-50 transition shadow-xs self-start sm:self-auto">
                Voltar à Fila
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        buscaPaciente: '',
        pacientesEncontrados: [],
        pacienteIdSelecionado: {{ $pacientePreSelecionado ? $pacientePreSelecionado->id : 'null' }},
        pacienteNomeSelecionado: '{{ $pacientePreSelecionado ? addslashes($pacientePreSelecionado->nome_completo . ' (CPF: ' . $pacientePreSelecionado->cpf_formatado . ')') : '' }}',
        buscando: false,

        buscarPacientes() {
            if (this.buscaPaciente.length < 2) {
                this.pacientesEncontrados = [];
                return;
            }
            this.buscando = true;
            fetch('{{ route('pacientes.index') }}?busca=' + encodeURIComponent(this.buscaPaciente), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                this.pacientesEncontrados = data;
                this.buscando = false;
            })
            .catch(() => { this.buscando = false; });
        },

        selecionarPaciente(p) {
            this.pacienteIdSelecionado = p.id;
            this.pacienteNomeSelecionado = p.nome + ' (CPF: ' + (p.cpf || 'S/N') + ')';
            this.pacientesEncontrados = [];
            this.buscaPaciente = '';
        }
    }">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 sm:p-8">
                <form method="POST" action="{{ route('demanda-reprimida.store') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="paciente_id" :value="pacienteIdSelecionado" required>

                    <!-- Seleção do Paciente -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Paciente Solicitante *</label>

                        <div x-show="!pacienteIdSelecionado">
                            <div class="relative">
                                <input type="text" x-model="buscaPaciente" @input.debounce.300ms="buscarPacientes()" placeholder="Buscar paciente por nome, CPF ou cartão SUS..." class="w-full rounded-lg border-slate-300 text-sm focus:ring-purple-500 focus:border-purple-500">
                                <div x-show="buscando" class="absolute right-3 top-2.5 text-xs text-slate-400">Buscando...</div>
                            </div>

                            <div x-show="pacientesEncontrados.length > 0" class="mt-1 border border-slate-200 rounded-lg bg-white shadow-lg max-h-48 overflow-y-auto divide-y divide-slate-100 z-50">
                                <template x-for="p in pacientesEncontrados" :key="p.id">
                                    <div @click="selecionarPaciente(p)" class="p-2.5 text-xs cursor-pointer hover:bg-purple-50 flex items-center justify-between">
                                        <div>
                                            <div class="font-bold text-slate-800" x-text="p.nome"></div>
                                            <div class="text-[11px] text-slate-500">CPF: <span x-text="p.cpf"></span> &bull; UBS: <span x-text="p.ubs"></span></div>
                                        </div>
                                        <span class="text-[10px] font-bold text-purple-700 bg-purple-100 px-2 py-0.5 rounded">Selecionar</span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div x-show="pacienteIdSelecionado" class="p-3 bg-purple-50 border border-purple-200 rounded-lg text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="text-purple-800 font-bold">Paciente Selecionado:</span>
                                <span class="text-slate-900 font-bold ml-1" x-text="pacienteNomeSelecionado"></span>
                            </div>
                            <button type="button" @click="pacienteIdSelecionado = null; pacienteNomeSelecionado = ''" class="text-rose-600 hover:text-rose-800 font-bold self-end sm:self-auto">Alterar</button>
                        </div>
                        @error('paciente_id') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Especialidade Solicitada -->
                    <div>
                        <label for="especialidade_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Especialidade Odontológica *</label>
                        <select id="especialidade_id" name="especialidade_id" required class="w-full rounded-lg border-slate-300 text-sm focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Selecione a especialidade...</option>
                            @foreach($especialidades as $esp)
                                <option value="{{ $esp->id }}" {{ old('especialidade_id') == $esp->id ? 'selected' : '' }}>
                                    {{ $esp->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('especialidade_id') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Prioridade -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Classificação de Risco (Prioridade) *</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50">
                                <input type="radio" name="prioridade" value="normal" {{ old('prioridade', 'normal') === 'normal' ? 'checked' : '' }} class="text-purple-600 focus:ring-purple-500">
                                <div class="ms-2">
                                    <div class="text-xs font-bold text-slate-800">Normal</div>
                                    <div class="text-[11px] text-slate-500">Ordem cronológica padrão</div>
                                </div>
                            </label>

                            <label class="flex items-center p-3 border border-rose-200 rounded-lg cursor-pointer bg-rose-50/50 hover:bg-rose-50">
                                <input type="radio" name="prioridade" value="urgente" {{ old('prioridade') === 'urgente' ? 'checked' : '' }} class="text-rose-600 focus:ring-rose-500">
                                <div class="ms-2">
                                    <div class="text-xs font-bold text-rose-800">Urgente</div>
                                    <div class="text-[11px] text-rose-600">Prioridade clínica imediata</div>
                                </div>
                            </label>
                        </div>
                        @error('prioridade') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Turno Preferencial -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Turno Preferencial de Atendimento *</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <label class="flex items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                <input type="radio" name="turno_preferencial" value="qualquer" {{ old('turno_preferencial', 'qualquer') === 'qualquer' ? 'checked' : '' }} class="text-purple-600 focus:ring-purple-500">
                                <div class="ms-2">
                                    <div class="text-xs font-bold text-slate-800">Qualquer</div>
                                    <div class="text-[10px] text-slate-500">Mais flexível</div>
                                </div>
                            </label>

                            <label class="flex items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                <input type="radio" name="turno_preferencial" value="manha" {{ old('turno_preferencial') === 'manha' ? 'checked' : '' }} class="text-purple-600 focus:ring-purple-500">
                                <div class="ms-2">
                                    <div class="text-xs font-bold text-slate-800">Manhã</div>
                                    <div class="text-[10px] text-slate-500">08h às 12h</div>
                                </div>
                            </label>

                            <label class="flex items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                <input type="radio" name="turno_preferencial" value="tarde" {{ old('turno_preferencial') === 'tarde' ? 'checked' : '' }} class="text-purple-600 focus:ring-purple-500">
                                <div class="ms-2">
                                    <div class="text-xs font-bold text-slate-800">Tarde</div>
                                    <div class="text-[10px] text-slate-500">13h às 17h</div>
                                </div>
                            </label>

                            <label class="flex items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                <input type="radio" name="turno_preferencial" value="noite" {{ old('turno_preferencial') === 'noite' ? 'checked' : '' }} class="text-purple-600 focus:ring-purple-500">
                                <div class="ms-2">
                                    <div class="text-xs font-bold text-slate-800">Noite</div>
                                    <div class="text-[10px] text-slate-500">18h às 22h</div>
                                </div>
                            </label>
                        </div>
                        @error('turno_preferencial') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Data da Solicitação -->
                    <div>
                        <label for="data_solicitacao" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Data da Solicitação / Encaminhamento *</label>
                        <input type="date" id="data_solicitacao" name="data_solicitacao" value="{{ old('data_solicitacao', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required class="w-full rounded-lg border-slate-300 text-sm focus:ring-purple-500 focus:border-purple-500">
                        @error('data_solicitacao') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Observações -->
                    <div>
                        <label for="observacoes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Observações do Encaminhamento da UBS</label>
                        <textarea id="observacoes" name="observacoes" rows="3" placeholder="Informações clínicas sobre o caso, hipótese diagnóstica ou motivo da urgência..." class="w-full rounded-lg border-slate-300 text-sm focus:ring-purple-500 focus:border-purple-500">{{ old('observacoes') }}</textarea>
                        @error('observacoes') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Botões -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('demanda-reprimida.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-200 transition">
                            Cancelar
                        </a>
                        <button type="submit" :disabled="!pacienteIdSelecionado" :class="!pacienteIdSelecionado ? 'opacity-50 cursor-not-allowed' : ''" class="px-6 py-2.5 bg-purple-600 text-white text-sm font-bold rounded-lg hover:bg-purple-700 transition shadow-xs">
                            Inserir na Fila
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
