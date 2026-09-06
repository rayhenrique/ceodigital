<!-- Sidebar Lateral (Desktop e Mobile Drawer) -->

<!-- 1. Off-canvas Drawer para Mobile & Tablet (< 1024px) -->
<div x-show="sidebarOpen" class="relative z-50 lg:hidden" role="dialog" aria-modal="true" style="display: none;">
    <!-- Backdrop de escurecimento com blur -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

    <!-- Gaveta Deslizante -->
    <div class="fixed inset-0 flex">
        <div x-show="sidebarOpen" 
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             @click.outside="sidebarOpen = false"
             class="relative mr-16 flex w-full max-w-xs flex-1 flex-col bg-white">
             
            <!-- Botão de Fechar da Gaveta -->
            <div class="absolute top-0 right-0 -mr-12 pt-4">
                <button type="button" @click="sidebarOpen = false" class="ml-1 flex h-10 w-10 items-center justify-center rounded-full text-white hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white">
                    <span class="sr-only">Fechar navegação</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Cabeçalho Mobile -->
            <div class="flex h-16 shrink-0 items-center px-6 border-b border-slate-200">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-teal-600 to-cyan-500 flex items-center justify-center text-white shadow-md shadow-teal-500/20 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-black tracking-tight text-slate-900">CEO <span class="text-teal-600">Digital</span></span>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">SUS Municipal</p>
                    </div>
                </a>
            </div>

            <!-- Links de Navegação Mobile (com scroll interno) -->
            <div class="flex-1 overflow-y-auto px-4 py-4 space-y-6">
                @include('layouts.partials.sidebar-nav-content')
            </div>

            <!-- Rodapé Mobile do Usuário -->
            <div class="border-t border-slate-200 p-4 bg-slate-50/70">
                @include('layouts.partials.sidebar-user-footer')
            </div>
        </div>
    </div>
</div>

<!-- 2. Sidebar Fixa para Desktop (>= 1024px) -->
<aside class="hidden lg:fixed lg:inset-y-0 lg:z-40 lg:flex lg:w-64 lg:flex-col bg-white border-r border-slate-200 shadow-xs no-print">
    <!-- Topo da Sidebar (Logo) -->
    <div class="flex h-16 shrink-0 items-center px-6 border-b border-slate-200">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-teal-600 to-cyan-500 flex items-center justify-center text-white shadow-md shadow-teal-500/20 group-hover:scale-105 transition-transform duration-200 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <span class="text-xl font-black tracking-tight text-slate-900">CEO <span class="text-teal-600">Digital</span></span>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">SUS Especializado</p>
            </div>
        </a>
    </div>

    <!-- Links de Navegação Desktop (Scroll Vertical Suave) -->
    <div class="flex-1 overflow-y-auto px-4 py-5 space-y-6">
        @include('layouts.partials.sidebar-nav-content')
    </div>

    <!-- Rodapé Desktop do Usuário -->
    <div class="border-t border-slate-200 p-4 bg-slate-50/50">
        @include('layouts.partials.sidebar-user-footer')
    </div>
</aside>
