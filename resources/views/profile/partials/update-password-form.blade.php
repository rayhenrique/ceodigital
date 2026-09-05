<section>
    <header>
        <h2 class="text-lg font-bold text-slate-900">
            Alterar Senha de Acesso
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Certifique-se de que sua conta utilize uma senha forte com caracteres alfanuméricos para manter a segurança do sistema.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-4">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Senha Atual')" class="text-xs font-semibold uppercase tracking-wider text-slate-700" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full text-sm" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1.5 text-xs text-rose-600" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('Nova Senha')" class="text-xs font-semibold uppercase tracking-wider text-slate-700" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full text-sm" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1.5 text-xs text-rose-600" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirmar Nova Senha')" class="text-xs font-semibold uppercase tracking-wider text-slate-700" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full text-sm" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1.5 text-xs text-rose-600" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition shadow-xs">
                Atualizar Senha
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-xs font-semibold text-emerald-600 flex items-center gap-1"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Senha atualizada com sucesso!
                </p>
            @endif
        </div>
    </form>
</section>
