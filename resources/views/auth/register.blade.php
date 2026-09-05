<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
            Cadastro de Usuário
        </h1>
        <p class="text-xs text-slate-500 mt-1">
            Preencha os dados institucionais para solicitar acesso
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nome Completo')" class="text-xs font-semibold uppercase tracking-wider text-slate-700" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <input id="name" class="block w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" 
                       type="text" 
                       name="name" 
                       :value="old('name')" 
                       placeholder="Seu nome completo" 
                       required 
                       autofocus 
                       autocomplete="name" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-xs text-rose-600" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('E-mail Institucional')" class="text-xs font-semibold uppercase tracking-wider text-slate-700" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
                </div>
                <input id="email" class="block w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" 
                       type="email" 
                       name="email" 
                       :value="old('email')" 
                       placeholder="seu.email@saude.gov.br" 
                       required 
                       autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-600" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Senha de Acesso')" class="text-xs font-semibold uppercase tracking-wider text-slate-700" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <input id="password" class="block w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" 
                       type="password" 
                       name="password" 
                       placeholder="••••••••"
                       required 
                       autocomplete="new-password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-600" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirmar Senha')" class="text-xs font-semibold uppercase tracking-wider text-slate-700" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <input id="password_confirmation" class="block w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" 
                       type="password" 
                       name="password_confirmation" 
                       placeholder="••••••••"
                       required 
                       autocomplete="new-password" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs text-rose-600" />
        </div>

        <div class="pt-2 flex flex-col gap-3">
            <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-teal-600 border border-transparent rounded-xl font-bold text-sm text-white hover:bg-teal-700 active:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition shadow-md shadow-teal-600/20">
                <svg class="w-4 h-4 text-teal-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>Criar Conta</span>
            </button>

            <a href="{{ route('login') }}" class="text-center text-xs font-semibold text-slate-600 hover:text-teal-600 transition">
                Já possui uma conta? <span class="underline">Fazer login</span>
            </a>
        </div>
    </form>
</x-guest-layout>
