<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
            Confirmação de Segurança
        </h1>
        <p class="text-xs text-slate-500 mt-2">
            Esta é uma área restrita e segura do sistema. Por favor, confirme sua senha antes de prosseguir.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

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
                       autocomplete="current-password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-600" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-teal-600 border border-transparent rounded-xl font-bold text-sm text-white hover:bg-teal-700 active:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition shadow-md shadow-teal-600/20">
                <svg class="w-4 h-4 text-teal-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Confirmar Autenticação</span>
            </button>
        </div>
    </form>
</x-guest-layout>
