<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
            Verificação de E-mail
        </h1>
        <p class="text-xs text-slate-500 mt-2">
            Obrigado por ingressar no sistema! Antes de começar a utilizar a plataforma, por favor confirme seu endereço de e-mail clicando no link de verificação que acabamos de enviar para sua caixa de entrada.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-medium">
            Um novo link de verificação foi enviado para o endereço de e-mail institucional informado durante seu cadastro.
        </div>
    @endif

    <div class="mt-4 flex flex-col gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-teal-600 border border-transparent rounded-xl font-bold text-sm text-white hover:bg-teal-700 active:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition shadow-md shadow-teal-600/20">
                <svg class="w-4 h-4 text-teal-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span>Reenviar E-mail de Verificação</span>
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf
            <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700 underline transition">
                Sair do Sistema
            </button>
        </form>
    </div>
</x-guest-layout>
