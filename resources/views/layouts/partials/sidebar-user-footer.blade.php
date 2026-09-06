<div class="flex items-center justify-between gap-3">
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-9 h-9 rounded-full bg-teal-100 text-teal-800 flex items-center justify-center font-bold text-xs border border-teal-200 shrink-0">
            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->name }}</p>
            <p class="text-[11px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
        </div>
    </div>
    @if(Auth::user()->isAdmin())
        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shrink-0">Admin</span>
    @else
        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200 shrink-0">Operador</span>
    @endif
</div>

<div class="mt-3 pt-2.5 border-t border-slate-200/80 flex items-center justify-between text-xs font-medium">
    <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-1.5 text-slate-600 hover:text-teal-700 py-1 px-2 rounded-lg hover:bg-white transition">
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        <span>Meu Perfil</span>
    </a>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="inline-flex items-center gap-1.5 text-rose-600 hover:text-rose-700 py-1 px-2 rounded-lg hover:bg-rose-50 transition">
            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            <span>Sair</span>
        </button>
    </form>
</div>
