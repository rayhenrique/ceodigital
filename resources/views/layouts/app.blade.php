<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CEO Digital') }} - Gestão Odontológica Especializada</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            @media print {
                aside, header, nav, .no-print, button, form {
                    display: none !important;
                }
                .lg\:pl-64 {
                    padding-left: 0 !important;
                }
                body {
                    background: #fff !important;
                    color: #000 !important;
                }
                .print-only {
                    display: block !important;
                }
                .shadow-sm, .shadow-md, .shadow-lg, .shadow-xl {
                    box-shadow: none !important;
                }
            }
        </style>
    </head>
    <body x-data="{ sidebarOpen: false }" class="font-sans antialiased text-slate-800 bg-slate-50 min-h-screen flex flex-col overflow-x-hidden">
        <!-- Sidebar Fixa no Desktop e Gaveta no Mobile -->
        @include('layouts.sidebar')

        <!-- Área de Conteúdo Principal (Deslocada da Sidebar no Desktop) -->
        <div class="lg:pl-64 flex flex-col min-h-screen">
            <!-- Topbar Minimalista -->
            @include('layouts.topbar')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white border-b border-slate-200 no-print">
                    <div class="max-w-7xl mx-auto py-4 sm:py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Session Feedback Messages -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full no-print">
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl shadow-xs">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm font-medium">{{ session('success') }}</span>
                        </div>
                        <button @click="show = false" class="text-emerald-600 hover:text-emerald-900">&times;</button>
                    </div>
                @endif

                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-center justify-between p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl shadow-xs">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm font-medium">{{ session('error') }}</span>
                        </div>
                        <button @click="show = false" class="text-rose-600 hover:text-rose-900">&times;</button>
                    </div>
                @endif

                @if (session('warning'))
                    <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-center justify-between p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl shadow-xs">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span class="text-sm font-medium">{{ session('warning') }}</span>
                        </div>
                        <button @click="show = false" class="text-amber-600 hover:text-amber-900">&times;</button>
                    </div>
                @endif

                @if ($errors->any())
                    <div x-data="{ show: true }" x-show="show" class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl shadow-xs">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <div class="text-sm font-bold">Por favor, corrija os erros abaixo:</div>
                                <ul class="list-disc list-inside text-xs mt-1 space-y-0.5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            <main class="flex-1 pb-12">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-200 py-4 no-print text-center text-xs text-slate-500">
                <div class="max-w-7xl mx-auto px-4">
                    CEO Digital &copy; {{ date('Y') }} - Sistema Municipal de Especialidades Odontológicas (SUS)
                </div>
            </footer>
        </div>
    </body>
</html>
