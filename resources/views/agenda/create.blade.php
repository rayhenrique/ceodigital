<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Novo Agendamento Odontológico
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Agende consultas regulares ou realize encaixes de acordo com as grades do CEO
                </p>
            </div>
            <a href="{{ route('agenda.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-50 transition shadow-xs">
                Voltar à Agenda
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
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 sm:p-8">
                <form method="POST" action="{{ route('agenda.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="paciente_id" :value="pacienteIdSelecionado" required>

                    <!-- Seleção do Paciente -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">1. Paciente a ser Atendido *</label>

                        <div x-show="!pacienteIdSelecionado">
                            <div class="relative">
                                <input type="text" x-model="buscaPaciente" @input.debounce.300ms="buscarPacientes()" placeholder="Digite nome, CPF ou cartão SUS do paciente..." class="w-full rounded-lg border-slate-300 text-sm focus:ring-teal-500 focus:border-teal-500">
                                <div x-show="buscando" class="absolute right-3 top-2.5 text-xs text-slate-400">Buscando...</div>
                            </div>

                            <div x-show="pacientesEncontrados.length > 0" class="mt-1 border border-slate-200 rounded-lg bg-white shadow-lg max-h-48 overflow-y-auto divide-y divide-slate-100 z-50">
                                <template x-for="p in pacientesEncontrados" :key="p.id">
                                    <div @click="selecionarPaciente(p)" class="p-2.5 text-xs cursor-pointer hover:bg-teal-50 flex items-center justify-between">
                                        <div>
                                            <div class="font-bold text-slate-800" x-text="p.nome"></div>
                                            <div class="text-[11px] text-slate-500">CPF: <span x-text="p.cpf"></span> &bull; UBS: <span x-text="p.ubs"></span></div>
                                        </div>
                                        <span class="text-[10px] font-bold text-teal-700 bg-teal-100 px-2 py-0.5 rounded">Selecionar</span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div x-show="pacienteIdSelecionado" class="p-3 bg-teal-50 border border-teal-200 rounded-lg text-xs flex items-center justify-between">
                            <div>
                                <span class="text-teal-800 font-bold">Paciente Selecionado:</span>
                                <span class="text-slate-900 font-bold ml-1" x-text="pacienteNomeSelecionado"></span>
                            </div>
                            <button type="button" @click="pacienteIdSelecionado = null; pacienteNomeSelecionado = ''" class="text-rose-600 hover:text-rose-800 font-bold">Alterar Paciente</button>
                        </div>
                        @error('paciente_id') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Dados do Agendamento -->
                    <div>
                        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">
                            2. Detalhes da Consulta
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Dentista -->
                            <div class="sm:col-span-2">
                                <label for="dentista_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Dentista Especialista *</label>
                                <select id="dentista_id" name="dentista_id" required class="w-full rounded-lg border-slate-300 text-sm focus:ring-teal-500 focus:border-teal-500">
                                    <option value="">Selecione o profissional...</option>
                                    @foreach($dentistas as $dent)
                                        <option value="{{ $dent->id }}" {{ old('dentista_id') == $dent->id ? 'selected' : '' }}>
                                            Dr(a). {{ $dent->nome_completo }} — {{ $dent->especialidade->nome ?? 'Geral' }} (CRO: {{ $dent->cro }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('dentista_id') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Data -->
                            <div>
                                <label for="data_agendamento" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Data do Atendimento *</label>
                                <input type="date" id="data_agendamento" name="data_agendamento" value="{{ old('data_agendamento', now()->toDateString()) }}" min="{{ now()->toDateString() }}" required class="w-full rounded-lg border-slate-300 text-sm focus:ring-teal-500 focus:border-teal-500">
                                @error('data_agendamento') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Turno -->
                            <div>
                                <label for="turno" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Turno *</label>
                                <select id="turno" name="turno" required class="w-full rounded-lg border-slate-300 text-sm focus:ring-teal-500 focus:border-teal-500">
                                    <option value="manha" {{ old('turno') === 'manha' ? 'selected' : '' }}>Manhã (07h às 12h)</option>
                                    <option value="tarde" {{ old('turno') === 'tarde' ? 'selected' : '' }}>Tarde (13h às 18h)</option>
                                    <option value="noite" {{ old('turno') === 'noite' ? 'selected' : '' }}>Noite (18h às 22h)</option>
                                </select>
                                @error('turno') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Tipo de Agendamento -->
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tipo de Vaga *</label>
                                <div class="flex items-center gap-6">
                                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                                        <input type="radio" name="tipo" value="normal" {{ old('tipo', 'normal') === 'normal' ? 'checked' : '' }} class="text-teal-600 focus:ring-teal-500">
                                        <span class="font-medium text-slate-800">Consulta Regular (Vaga de Grade)</span>
                                    </label>
                                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                                        <input type="radio" name="tipo" value="encaixe" {{ old('tipo') === 'encaixe' ? 'checked' : '' }} class="text-rose-600 focus:ring-rose-500">
                                        <span class="font-medium text-rose-700">Encaixe de Urgência (Limite 2/turno)</span>
                                    </label>
                                </div>
                                @error('tipo') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Observações -->
                            <div class="sm:col-span-2">
                                <label for="observacoes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Observações Clínicas / Encaminhamento</label>
                                <textarea id="observacoes" name="observacoes" rows="3" placeholder="Informações relevantes sobre a queixa ou encaminhamento da UBS..." class="w-full rounded-lg border-slate-300 text-sm focus:ring-teal-500 focus:border-teal-500">{{ old('observacoes') }}</textarea>
                                @error('observacoes') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('agenda.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-200 transition">
                            Cancelar
                        </a>
                        <button type="submit" :disabled="!pacienteIdSelecionado" :class="!pacienteIdSelecionado ? 'opacity-50 cursor-not-allowed' : ''" class="px-6 py-2.5 bg-teal-600 text-white text-sm font-bold rounded-lg hover:bg-teal-700 transition shadow-xs">
                            Confirmar Agendamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
