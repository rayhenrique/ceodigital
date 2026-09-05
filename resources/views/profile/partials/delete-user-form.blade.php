<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-rose-900">
            Excluir Conta de Usuário
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Uma vez que sua conta for excluída, todos os seus recursos e dados serão desativados permanentemente. Antes de excluir sua conta, certifique-se de que não possui pendências operacionais no sistema.
        </p>
    </header>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-rose-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 transition shadow-xs"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        Excluir Minha Conta
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <div class="flex items-center gap-3 text-rose-600 mb-2">
                <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h2 class="text-lg font-bold text-slate-900">
                    Tem certeza de que deseja excluir sua conta?
                </h2>
            </div>

            <p class="mt-2 text-sm text-slate-500">
                Esta ação é irreversível. Todas as suas credenciais serão revogadas imediatamente. Digite sua senha atual para confirmar a exclusão permanente.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Senha de Acesso" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full text-sm"
                    placeholder="Digite sua senha para confirmar"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-xs text-rose-600" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Cancelar
                </button>

                <button type="submit" class="px-5 py-2 bg-rose-600 border border-transparent rounded-xl text-xs font-semibold text-white hover:bg-rose-700 transition shadow-xs">
                    Confirmar Exclusão
                </button>
            </div>
        </form>
    </x-modal>
</section>
