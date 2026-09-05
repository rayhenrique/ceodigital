<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                Relatórios Gerenciais & Indicadores SUS
            </h2>
            <p class="text-sm text-slate-500 mt-1">
                Monitoramento de desempenho, absenteísmo, demanda reprimida e produtividade clínica
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Card 1: Absenteísmo e Faltas -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                        </div>
                        <h3 class="font-bold text-lg text-slate-900">Relatório de Absenteísmo & Faltas</h3>
                        <p class="text-sm text-slate-500 mt-2">
                            Acompanhamento de faltas de pacientes por Unidade Básica de Saúde (UBS) de origem e especialidade, com taxa percentual e relação nominal.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <a href="{{ route('relatorios.absenteismo') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-rose-600 text-white rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-rose-700 transition shadow-xs">
                            Gerar Relatório
                        </a>
                    </div>
                </div>

                <!-- Card 2: Produção Odontológica -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <h3 class="font-bold text-lg text-slate-900">Produção por Dentista</h3>
                        <p class="text-sm text-slate-500 mt-2">
                            Consolidado de atendimentos clínicos concluídos por cirurgião-dentista, segregando vagas regulares de grade e encaixes de urgência.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <a href="{{ route('relatorios.producao') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-teal-600 text-white rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-teal-700 transition shadow-xs">
                            Gerar Relatório
                        </a>
                    </div>
                </div>

                <!-- Card 3: Demanda Reprimida & Tempo de Espera -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="font-bold text-lg text-slate-900">Demanda Reprimida & Espera</h3>
                        <p class="text-sm text-slate-500 mt-2">
                            Diagnóstico do volume de cidadãos aguardando vaga por especialidade, tempo médio de espera em dias e estratificação por urgência.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <a href="{{ route('relatorios.demanda-reprimida') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-purple-600 text-white rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-purple-700 transition shadow-xs">
                            Gerar Relatório
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
