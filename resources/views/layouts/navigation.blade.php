<nav x-data="{ open: false }" class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-slate-800" />
                    </a>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-8 md:flex items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="px-3 py-2 text-sm font-medium">
                        <svg class="w-4 h-4 me-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        {{ __('Painel') }}
                    </x-nav-link>

                    <x-nav-link :href="route('agenda.index')" :active="request()->routeIs('agenda.*')" class="px-3 py-2 text-sm font-medium">
                        <svg class="w-4 h-4 me-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ __('Agenda') }}
                    </x-nav-link>

                    <x-nav-link :href="route('pacientes.index')" :active="request()->routeIs('pacientes.*')" class="px-3 py-2 text-sm font-medium">
                        <svg class="w-4 h-4 me-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        {{ __('Pacientes') }}
                    </x-nav-link>

                    <x-nav-link :href="route('demanda-reprimida.index')" :active="request()->routeIs('demanda-reprimida.*')" class="px-3 py-2 text-sm font-medium">
                        <svg class="w-4 h-4 me-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('Fila de Espera') }}
                    </x-nav-link>

                    <!-- Dropdown Cadastros / Estrutura -->
                    <div class="relative" x-data="{ dropOpen: false }" @click.outside="dropOpen = false">
                        <button @click="dropOpen = !dropOpen" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
                            <span>Cadastros</span>
                            <svg class="ms-1 h-4 w-4 fill-current opacity-60" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="dropOpen" x-transition class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-slate-100 py-1 z-50">
                            <a href="{{ route('dentistas.index') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-teal-50 hover:text-teal-800">
                                <svg class="w-4 h-4 me-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Dentistas & Grades
                            </a>
                            <a href="{{ route('especialidades.index') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-teal-50 hover:text-teal-800">
                                <svg class="w-4 h-4 me-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                Especialidades
                            </a>
                            <a href="{{ route('ubs.index') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-teal-50 hover:text-teal-800">
                                <svg class="w-4 h-4 me-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                UBS de Origem
                            </a>
                        </div>
                    </div>

                    <x-nav-link :href="route('relatorios.index')" :active="request()->routeIs('relatorios.*')" class="px-3 py-2 text-sm font-medium">
                        <svg class="w-4 h-4 me-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        {{ __('Relatórios') }}
                    </x-nav-link>

                    @if(Auth::user()->isAdmin())
                    <!-- Admin Dropdown -->
                    <div class="relative" x-data="{ adminDrop: false }" @click.outside="adminDrop = false">
                        <button @click="adminDrop = !adminDrop" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-amber-700 bg-amber-50 hover:bg-amber-100 transition">
                            <svg class="w-4 h-4 me-1.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Gestão</span>
                            <svg class="ms-1 h-4 w-4 fill-current" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="adminDrop" x-transition class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-slate-100 py-1 z-50">
                            <a href="{{ route('users.index') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-900">
                                <svg class="w-4 h-4 me-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                Usuários do Sistema
                            </a>
                            <a href="{{ route('auditorias.index') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-900">
                                <svg class="w-4 h-4 me-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                Log de Auditoria
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Settings & Profile (Desktop) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <!-- Role Badge -->
                @if(Auth::user()->isAdmin())
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Administrador
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                        Operador
                    </span>
                @endif

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-slate-200 text-sm leading-4 font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 focus:outline-none transition">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-xs">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                <span class="font-medium text-slate-800">{{ Auth::user()->name }}</span>
                            </div>
                            <svg class="ms-2 h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Meu Perfil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();" class="text-rose-600 hover:bg-rose-50 hover:text-rose-700">
                                {{ __('Sair do Sistema') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Mobile Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-200 bg-white">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Painel') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('agenda.index')" :active="request()->routeIs('agenda.*')">
                {{ __('Agenda') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('pacientes.index')" :active="request()->routeIs('pacientes.*')">
                {{ __('Pacientes') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('demanda-reprimida.index')" :active="request()->routeIs('demanda-reprimida.*')">
                {{ __('Fila de Espera') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dentistas.index')" :active="request()->routeIs('dentistas.*')">
                {{ __('Dentistas & Grades') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('especialidades.index')" :active="request()->routeIs('especialidades.*')">
                {{ __('Especialidades') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('ubs.index')" :active="request()->routeIs('ubs.*')">
                {{ __('UBS de Origem') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('relatorios.index')" :active="request()->routeIs('relatorios.*')">
                {{ __('Relatórios') }}
            </x-responsive-nav-link>
            @if(Auth::user()->isAdmin())
                <div class="px-4 pt-2 text-xs font-semibold uppercase text-amber-700">Administração</div>
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                    {{ __('Usuários') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('auditorias.index')" :active="request()->routeIs('auditorias.*')">
                    {{ __('Auditoria') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-3 border-t border-slate-200 px-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-semibold text-slate-800">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-slate-500">{{ Auth::user()->email }}</div>
                </div>
                @if(Auth::user()->isAdmin())
                    <span class="px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-800">Admin</span>
                @else
                    <span class="px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800">Operador</span>
                @endif
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Meu Perfil') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();" class="text-rose-600">
                        {{ __('Sair') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
