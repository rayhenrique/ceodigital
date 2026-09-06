<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
            Recuperação de Senha
        </h1>
        <p class="text-xs text-slate-500 mt-2">
            Esqueceu sua senha? Sem problemas. Digite seu e-mail institucional e enviaremos um link seguro para você redefinir sua senha de acesso.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

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
                       autofocus />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-600" />
        </div>

        <div class="pt-2 flex flex-col gap-3">
            <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-teal-600 border border-transparent rounded-xl font-bold text-sm text-white hover:bg-teal-700 active:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition shadow-md shadow-teal-600/20">
                <svg class="w-4 h-4 text-teal-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span>Enviar Link de Recuperação</span>
            </button>

            <div class="p-3 bg-amber-50 border border-amber-200/70 rounded-xl text-center">
                <p class="text-[11px] text-amber-800">
                    <strong>Sem acesso ao e-mail?</strong> O Administrador do sistema pode redefinir sua senha instantaneamente pelo painel de Gestão de Usuários.
                </p>
            </div>

            <a href="{{ route('login') }}" class="text-center text-xs font-semibold text-slate-600 hover:text-teal-600 transition">
                &larr; Voltar para a tela de login
            </a>
        </div>
    </form>
</x-guest-layout>
