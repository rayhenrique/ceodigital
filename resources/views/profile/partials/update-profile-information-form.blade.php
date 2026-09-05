<section>
    <header>
        <h2 class="text-lg font-bold text-slate-900">
            Informações do Perfil
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Atualize seu nome completo e endereço de e-mail institucional associado a esta conta.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-4">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nome Completo')" class="text-xs font-semibold uppercase tracking-wider text-slate-700" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full text-sm" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-1.5 text-xs text-rose-600" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('E-mail Institucional')" class="text-xs font-semibold uppercase tracking-wider text-slate-700" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full text-sm" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-1.5 text-xs text-rose-600" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                    <p>
                        Seu endereço de e-mail ainda não foi verificado.

                        <button form="send-verification" class="font-semibold underline hover:text-amber-900 focus:outline-none">
                            Clique aqui para reenviar o e-mail de verificação.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-emerald-700">
                            Um novo link de verificação foi enviado para o seu endereço de e-mail.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition shadow-xs">
                Salvar Alterações
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-xs font-semibold text-emerald-600 flex items-center gap-1"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Informações salvas com sucesso!
                </p>
            @endif
        </div>
    </form>
</section>
