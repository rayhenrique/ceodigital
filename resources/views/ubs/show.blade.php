<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ $ubs->nome }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Unidade Básica de Saúde da Atenção Primária
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('ubs.edit', $ubs) }}" class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg font-semibold text-xs uppercase tracking-widest hover:bg-teal-700 transition shadow-xs">
                    Editar UBS
                </a>
                
                @if($ubs->pacientes_count > 0)
                    <button type="button" 
                            onclick="alert('Esta UBS possui {{ $ubs->pacientes_count }} paciente(s) vinculado(s) e não pode ser excluída. Transfira os pacientes antes de prosseguir.')" 
                            class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-400 rounded-lg font-semibold text-xs uppercase tracking-widest cursor-not-allowed transition shadow-xs" 
                            title="Não é possível excluir: existem pacientes vinculados">
                        Excluir UBS
                    </button>
                @else
                    <form method="POST" action="{{ route('ubs.destroy', $ubs) }}" onsubmit="return confirm('Deseja realmente excluir a UBS \'{{ addslashes($ubs->nome) }}\'? Esta ação é irreversível.')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-rose-600 text-white rounded-lg font-semibold text-xs uppercase tracking-widest hover:bg-rose-700 transition shadow-xs">
                            Excluir UBS
                        </button>
                    </form>
                @endif

                <a href="{{ route('ubs.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg font-semibold text-xs uppercase tracking-widest hover:bg-slate-50 transition shadow-xs">
                    Voltar à Lista
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Ficha da UBS -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Informações da Unidade</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm">
                    <div>
                        <div class="text-xs text-slate-400">Endereço</div>
                        <div class="font-medium text-slate-800">{{ $ubs->endereco ?? 'Não informado' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400">Diretor(a) / Coordenação</div>
                        <div class="font-medium text-slate-800">{{ $ubs->diretor ?? 'Não informado' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400">Contato Oficial</div>
                        <div class="font-mono text-slate-800">{{ $ubs->contato ?? 'Não informado' }}</div>
                    </div>
                </div>
            </div>

            <!-- Pacientes Vinculados -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                    <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                        <span>Pacientes Vinculados a esta UBS</span>
                        <span class="text-xs bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">{{ $ubs->pacientes_count ?? $ubs->pacientes->count() }}</span>
                    </h3>
                </div>

                @if($ubs->pacientes->isEmpty())
                    <div class="p-8 text-center text-slate-400 text-sm">
                        Nenhum paciente cadastrado vinculado a esta UBS até o momento.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600 min-w-[550px]">
                            <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3">Nome do Paciente</th>
                                    <th class="px-4 py-3">CPF</th>
                                    <th class="px-4 py-3">Contato</th>
                                    <th class="px-4 py-3">ACS</th>
                                    <th class="px-4 py-3 text-right">Ação</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($ubs->pacientes as $paciente)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-3 font-bold text-slate-900">
                                            <a href="{{ route('pacientes.show', $paciente) }}" class="hover:text-teal-600">
                                                {{ $paciente->nome_completo }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 font-mono text-xs">{{ $paciente->cpf_formatado }}</td>
                                        <td class="px-4 py-3 text-xs">{{ $paciente->telefone_1 }}</td>
                                        <td class="px-4 py-3 text-xs text-slate-500">{{ $paciente->nome_acs ?? '-' }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('pacientes.show', $paciente) }}" class="text-xs text-teal-600 font-bold hover:underline">
                                                Ver Prontuário
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
