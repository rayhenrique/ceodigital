<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Editar Dentista & Escala
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Dr(a). {{ $dentista->nome_completo }} &bull; CRO: {{ $dentista->cro }}
                </p>
            </div>
            <a href="{{ route('dentistas.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-50 transition shadow-xs self-start sm:self-auto">
                Voltar à Lista
            </a>
        </div>
    </x-slot>

    @php
        $gradesJson = $dentista->grades->map(fn($g) => [
            'dia_semana' => (string)$g->dia_semana,
            'turno' => (string)$g->turno,
            'vagas_padrao' => (int)$g->vagas_padrao
        ])->values()->toJson();
    @endphp

    <div class="py-8" x-data="{
        grades: {{ $gradesJson }},
        adicionarGrade() {
            this.grades.push({ dia_semana: 1, turno: 'manha', vagas_padrao: 8 });
        },
        removerGrade(index) {
            this.grades.splice(index, 1);
        }
    }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 sm:p-8">
                <form method="POST" action="{{ route('dentistas.update', $dentista) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Dados Principais -->
                    <div>
                        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">
                            1. Dados do Profissional
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Nome Completo -->
                            <div class="sm:col-span-2">
                                <label for="nome_completo" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nome Completo do Dentista *</label>
                                <input type="text" id="nome_completo" name="nome_completo" value="{{ old('nome_completo', $dentista->nome_completo) }}" required class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                                @error('nome_completo') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- CRO -->
                            <div>
                                <label for="cro" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Registro CRO (com UF) *</label>
                                <input type="text" id="cro" name="cro" value="{{ old('cro', $dentista->cro) }}" required class="w-full rounded-lg border-slate-300 text-sm font-mono focus:border-teal-500 focus:ring-teal-500">
                                @error('cro') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Especialidade -->
                            <div>
                                <label for="especialidade_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Especialidade Odontológica *</label>
                                <select id="especialidade_id" name="especialidade_id" required class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                                    @foreach($especialidades as $esp)
                                        <option value="{{ $esp->id }}" {{ old('especialidade_id', $dentista->especialidade_id) == $esp->id ? 'selected' : '' }}>
                                            {{ $esp->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('especialidade_id') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Telefone -->
                            <div>
                                <label for="telefone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Telefone / Celular</label>
                                <input type="text" id="telefone" name="telefone" value="{{ old('telefone', $dentista->telefone) }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                                @error('telefone') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex items-center gap-2 pt-6">
                                <input type="checkbox" id="status_ativo" name="status_ativo" value="1" {{ old('status_ativo', $dentista->status_ativo) ? 'checked' : '' }} class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                                <label for="status_ativo" class="text-sm font-semibold text-slate-700">Dentista Ativo no Corpo Clínico</label>
                            </div>
                        </div>
                    </div>

                    <!-- Grade Semanal de Atendimento (Alpine.js) -->
                    <div>
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                            <div>
                                <h3 class="text-base font-bold text-slate-900">
                                    2. Grade Semanal de Atendimento
                                </h3>
                                <p class="text-xs text-slate-500">Configure os dias e turnos em que o profissional atende no CEO</p>
                            </div>
                            <button type="button" @click="adicionarGrade()" class="inline-flex items-center px-3 py-1.5 bg-teal-50 text-teal-700 border border-teal-200 rounded-lg text-xs font-bold hover:bg-teal-100 transition">
                                <svg class="w-3.5 h-3.5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Adicionar Turno
                            </button>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(grade, index) in grades" :key="index">
                                <div class="flex flex-col sm:flex-row items-center gap-3 p-3 bg-slate-50 border border-slate-200 rounded-lg">
                                    <!-- Dia da Semana -->
                                    <div class="w-full sm:w-1/3">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Dia da Semana</label>
                                        <select :name="'grades[' + index + '][dia_semana]'" x-model="grade.dia_semana" required class="w-full rounded border-slate-300 text-sm focus:ring-teal-500">
                                            <option value="1">Segunda-feira</option>
                                            <option value="2">Terça-feira</option>
                                            <option value="3">Quarta-feira</option>
                                            <option value="4">Quinta-feira</option>
                                            <option value="5">Sexta-feira</option>
                                            <option value="6">Sábado</option>
                                        </select>
                                    </div>

                                    <!-- Turno -->
                                    <div class="w-full sm:w-1/3">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Turno</label>
                                        <select :name="'grades[' + index + '][turno]'" x-model="grade.turno" required class="w-full rounded border-slate-300 text-sm focus:ring-teal-500">
                                            <option value="manha">Manhã (07h às 12h)</option>
                                            <option value="tarde">Tarde (13h às 18h)</option>
                                            <option value="noite">Noite (18h às 22h)</option>
                                        </select>
                                    </div>

                                    <!-- Vagas Padrão -->
                                    <div class="w-full sm:w-1/4">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Vagas / Turno</label>
                                        <input type="number" :name="'grades[' + index + '][vagas_padrao]'" x-model="grade.vagas_padrao" min="1" max="50" required class="w-full rounded border-slate-300 text-sm focus:ring-teal-500">
                                    </div>

                                    <!-- Remover -->
                                    <div class="pt-5 sm:pt-4">
                                        <button type="button" @click="removerGrade(index)" class="p-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded transition" title="Remover Turno">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('dentistas.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-200 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-teal-600 text-white text-sm font-bold rounded-lg hover:bg-teal-700 transition shadow-xs">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
