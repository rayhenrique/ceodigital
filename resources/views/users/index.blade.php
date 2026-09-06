<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Gestão de Usuários
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Operadores e Administradores do sistema CEO Digital
                </p>
            </div>
            <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 transition shadow-xs">
                <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Novo Usuário
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Filtro -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <form method="GET" action="{{ route('users.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <input type="text" name="busca" value="{{ $busca ?? '' }}" placeholder="Buscar usuário por nome ou e-mail..." class="w-full pl-4 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    <div class="w-full sm:w-48">
                        <select name="role" class="w-full py-2 border border-slate-300 rounded-lg text-sm focus:ring-amber-500 focus:border-amber-500">
                            <option value="">Todos os Perfis</option>
                            <option value="admin" {{ ($role ?? '') === 'admin' ? 'selected' : '' }}>Administradores</option>
                            <option value="operador" {{ ($role ?? '') === 'operador' ? 'selected' : '' }}>Operadores</option>
                        </select>
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="submit" class="flex-1 sm:flex-initial px-5 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-900 transition">
                            Filtrar
                        </button>
                        @if($busca || $role)
                            <a href="{{ route('users.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-200 transition text-center">
                                Limpar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 min-w-[650px]">
                        <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3.5">Nome / E-mail</th>
                                <th class="px-4 py-3.5 text-center">Perfil de Acesso</th>
                                <th class="px-4 py-3.5 text-center">Status</th>
                                <th class="px-4 py-3.5">Criado em</th>
                                <th class="px-4 py-3.5 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($users as $userItem)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-3.5">
                                        <div class="font-bold text-slate-900">{{ $userItem->name }}</div>
                                        <div class="text-xs text-slate-500">{{ $userItem->email }}</div>
                                    </td>
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        @if($userItem->isAdmin())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                                Administrador
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                                Operador
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        @if($userItem->status_ativo)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-teal-50 text-teal-800">
                                                Ativo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-rose-50 text-rose-800">
                                                Bloqueado
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-xs text-slate-500 whitespace-nowrap">
                                        {{ $userItem->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('users.edit', $userItem) }}" class="p-1.5 text-slate-500 hover:text-slate-800 rounded transition" title="Editar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            @if($userItem->id !== Auth::id())
                                                <form method="POST" action="{{ route('users.destroy', $userItem) }}" onsubmit="return confirm('Deseja realmente remover este usuário?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 rounded transition" title="Excluir">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                        Nenhum usuário localizado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
