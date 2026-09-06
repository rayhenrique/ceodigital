<!-- Seção 1: Atendimento & Fluxo Clínico -->
<div>
    <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-3 mb-2">
        Atendimento & Fluxo
    </h3>
    <nav class="space-y-1">
        <x-sidebar-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <x-slot name="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </x-slot>
            Painel Geral
        </x-sidebar-nav-link>

        <x-sidebar-nav-link :href="route('agenda.index')" :active="request()->routeIs('agenda.*')">
            <x-slot name="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </x-slot>
            Agenda de Atendimentos
        </x-sidebar-nav-link>

        <x-sidebar-nav-link :href="route('demanda-reprimida.index')" :active="request()->routeIs('demanda-reprimida.*')">
            <x-slot name="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </x-slot>
            Demanda Reprimida (Fila)
        </x-sidebar-nav-link>

        <x-sidebar-nav-link :href="route('pacientes.index')" :active="request()->routeIs('pacientes.*')">
            <x-slot name="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </x-slot>
            Pacientes
        </x-sidebar-nav-link>
    </nav>
</div>

<!-- Seção 2: Cadastros & Estrutura -->
<div>
    <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-3 mb-2">
        Cadastros Clínicos
    </h3>
    <nav class="space-y-1">
        <x-sidebar-nav-link :href="route('dentistas.index')" :active="request()->routeIs('dentistas.*')">
            <x-slot name="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </x-slot>
            Dentistas & Escalas
        </x-sidebar-nav-link>

        <x-sidebar-nav-link :href="route('especialidades.index')" :active="request()->routeIs('especialidades.*')">
            <x-slot name="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </x-slot>
            Especialidades
        </x-sidebar-nav-link>

        <x-sidebar-nav-link :href="route('ubs.index')" :active="request()->routeIs('ubs.*')">
            <x-slot name="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </x-slot>
            Unidades Básicas (UBS)
        </x-sidebar-nav-link>
    </nav>
</div>

<!-- Seção 3: Relatórios & Indicadores -->
<div>
    <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-3 mb-2">
        Inteligência & Gestão
    </h3>
    <nav class="space-y-1">
        <x-sidebar-nav-link :href="route('relatorios.index')" :active="request()->routeIs('relatorios.*')">
            <x-slot name="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </x-slot>
            Relatórios & Indicadores
        </x-sidebar-nav-link>
    </nav>
</div>

<!-- Seção 4: Administração do Sistema (Apenas Administradores) -->
@if(Auth::user()->isAdmin())
<div>
    <h3 class="text-[11px] font-bold uppercase tracking-wider text-amber-600 px-3 mb-2 flex items-center justify-between">
        <span>Administração</span>
        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
    </h3>
    <nav class="space-y-1">
        <x-sidebar-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
            <x-slot name="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </x-slot>
            Gestão de Usuários
        </x-sidebar-nav-link>

        <x-sidebar-nav-link :href="route('auditorias.index')" :active="request()->routeIs('auditorias.*')">
            <x-slot name="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </x-slot>
            Trilha de Auditoria
        </x-sidebar-nav-link>
    </nav>
</div>
@endif
