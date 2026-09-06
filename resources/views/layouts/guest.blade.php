<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CEO Digital') }} - Acesso ao Sistema</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-800 antialiased bg-slate-50 min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden selection:bg-teal-500 selection:text-white">
        <!-- Subtle background glow circles -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-teal-200/40 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-cyan-200/30 rounded-full blur-3xl pointer-events-none"></div>

        <div class="w-full max-w-md relative z-10">
            <!-- Logo Header -->
            <div class="flex flex-col items-center mb-6">
                <a href="{{ route('landing') }}" class="group flex items-center gap-3 transition-transform hover:scale-105">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-teal-600 to-cyan-500 flex items-center justify-center text-white shadow-lg shadow-teal-600/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div class="flex flex-col text-left leading-tight">
                        <span class="font-black text-2xl tracking-tight text-slate-900">CEO <span class="text-teal-600">Digital</span></span>
                        <span class="text-xs font-semibold text-slate-400 tracking-wider uppercase">SUS Municipal</span>
                    </div>
                </a>
            </div>

            <!-- Card Body -->
            <div class="bg-white px-6 py-8 sm:p-8 shadow-xl shadow-slate-200/60 rounded-2xl border border-slate-100">
                {{ $slot }}
            </div>

            <!-- Footer Return Link & Institutional Note -->
            <div class="mt-6 text-center space-y-2">
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-teal-600 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Voltar à página inicial</span>
                </a>
                <p class="text-[11px] text-slate-400">
                    Sistema de Informatização do Centro de Especialidades Odontológicas
                </p>
                <p class="text-[11px] text-slate-400">
                    Desenvolvido por <a href="https://kltecnologia.com" target="_blank" rel="noopener noreferrer" class="font-medium text-slate-600 hover:text-teal-600 hover:underline transition">KL Tecnologia</a>
                </p>
            </div>
        </div>
    </body>
</html>
