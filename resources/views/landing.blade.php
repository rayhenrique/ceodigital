<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CEO Digital - Centro de Especialidades Odontológicas</title>
    <meta name="description" content="Sistema de Informatização e Gestão de Consultas Especializadas do Centro de Especialidades Odontológicas (CEO).">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800 selection:bg-teal-500 selection:text-white">

    <!-- Header / Navbar Superior -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-teal-600 to-cyan-500 flex items-center justify-center text-white shadow-lg shadow-teal-500/20">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <span class="text-2xl font-black tracking-tight text-slate-900">CEO <span class="text-teal-600">Digital</span></span>
                    <p class="text-xs text-slate-500 font-medium tracking-wider uppercase">Centro de Especialidades Odontológicas</p>
                </div>
            </div>

            <nav class="flex items-center gap-4">
                <a href="#especialidades" class="text-sm font-semibold text-slate-600 hover:text-teal-600 transition">Especialidades</a>
                <a href="#horarios" class="text-sm font-semibold text-slate-600 hover:text-teal-600 transition">Atendimento</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-teal-600 text-white font-semibold text-sm shadow-md hover:bg-teal-700 transition">
                        <span>Acessar Painel</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 text-white font-semibold text-sm shadow-md hover:bg-slate-800 transition">
                        <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                        <span>Acesso Restrito</span>
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-28 bg-gradient-to-b from-teal-50/50 via-white to-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="text-center max-w-3xl mx-auto">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-teal-100/80 text-teal-800 text-xs font-bold uppercase tracking-wider mb-6">
                    <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                    Rede de Atenção Especializada do SUS
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight mb-6">
                    Saúde bucal avançada e acolhimento para todos os cidadãos.
                </h1>
                <p class="text-lg sm:text-xl text-slate-600 font-normal leading-relaxed mb-10">
                    O <strong>Centro de Especialidades Odontológicas (CEO)</strong> atua como referência para a atenção primária, oferecendo procedimentos especializados, cirurgias, reabilitação protética e diagnóstico precoce com controle informatizado de vagas.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <a href="#especialidades" class="px-6 py-3.5 rounded-xl bg-teal-600 text-white font-semibold shadow-lg shadow-teal-500/25 hover:bg-teal-700 transition">
                        Conhecer Especialidades
                    </a>
                    <a href="#horarios" class="px-6 py-3.5 rounded-xl bg-white border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 shadow-sm transition">
                        Horários e Funcionamento
                    </a>
                </div>
            </div>

            <!-- Badges de Destaque -->
            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 mb-1">Encaminhamento via UBS</h3>
                        <p class="text-sm text-slate-600">Acesso referenciado a partir da triagem prévia realizada na sua Unidade Básica de Saúde.</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-cyan-50 flex items-center justify-center text-cyan-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 mb-1">Fila de Espera Transparente</h3>
                        <p class="text-sm text-slate-600">Controle rigoroso da demanda reprimida com priorização ética de casos de urgência.</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 mb-1">Equipe Multidisciplinar</h3>
                        <p class="text-sm text-slate-600">Especialistas qualificados em estomatologia, endodontia, periodontia e prótese.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção de Especialidades (RF02) -->
    <section id="especialidades" class="py-20 bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-teal-600 font-bold text-sm uppercase tracking-wider">Atenção Especializada</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2">Especialidades Oferecidas</h2>
                <p class="text-slate-600 mt-3">Serviços odontológicos de média e alta complexidade regulados pela equipe do CEO.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($especialidades as $esp)
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-6 hover:shadow-md hover:border-teal-300 transition group flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center font-black text-lg mb-4 group-hover:bg-teal-600 group-hover:text-white transition">
                                {{ substr($esp->nome, 0, 1) }}
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2">{{ $esp->nome }}</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">{{ $esp->descricao ?? 'Atendimento especializado aos pacientes referenciados pela rede básica.' }}</p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-200/60 flex items-center justify-between text-xs font-semibold text-teal-600">
                            <span>Ativo no SUS</span>
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-center py-12 text-slate-500">
                        Nenhuma especialidade ativa cadastrada no momento.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Horários e Turnos (RF01) -->
    <section id="horarios" class="py-20 bg-slate-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-teal-400 font-bold text-sm uppercase tracking-wider">Escala Operacional</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-2">Horários de Atendimento</h2>
                <p class="text-slate-400 mt-3">Consultas organizadas em três turnos de Segunda a Sábado.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-6 text-center hover:border-teal-400 transition">
                    <div class="inline-flex p-3 rounded-full bg-amber-500/10 text-amber-400 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Turno da Manhã</h3>
                    <p class="text-2xl font-black text-teal-400 mb-2">08:00 às 12:00</p>
                    <p class="text-xs text-slate-400">Atendimentos pré-agendados e triagem inicial de demandas prioritárias.</p>
                </div>

                <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-6 text-center hover:border-teal-400 transition">
                    <div class="inline-flex p-3 rounded-full bg-teal-500/10 text-teal-400 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Turno da Tarde</h3>
                    <p class="text-2xl font-black text-teal-400 mb-2">13:00 às 17:00</p>
                    <p class="text-xs text-slate-400">Cirurgias orais, endodontia e procedimentos de reabilitação.</p>
                </div>

                <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-6 text-center hover:border-teal-400 transition">
                    <div class="inline-flex p-3 rounded-full bg-indigo-500/10 text-indigo-400 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Turno da Noite</h3>
                    <p class="text-2xl font-black text-teal-400 mb-2">18:00 às 22:00</p>
                    <p class="text-xs text-slate-400">Atendimento a trabalhadores e consultas especializadas noturnas.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Rodapé Discreto (RF03) -->
    <footer class="bg-slate-950 text-slate-400 py-10 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-center sm:text-left">
                <span class="font-bold text-white">CEO Digital</span> &copy; {{ date('Y') }} - Secretaria Municipal de Saúde.
                <p class="text-xs text-slate-500 mt-1">Desenvolvido sob as diretrizes do Programa Brasil Sorridente / SUS.</p>
            </div>
            <div>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-400 hover:text-teal-400 transition py-1 px-3 rounded-lg border border-slate-800 hover:border-slate-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    <span>Acesso de Operadores e Administradores</span>
                </a>
            </div>
        </div>
    </footer>

</body>
</html>
