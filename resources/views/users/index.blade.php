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

    <div class="py-8" x-data="{
        resetModalOpen: false,
        targetUser: { id: null, name: '', email: '' },
        newPassword: '',
        newPasswordConfirmation: '',
        showPassword: false,
        passwordCopied: false,
        abrirReset(id, name, email) {
            this.targetUser = { id, name, email };
            this.newPassword = '';
            this.newPasswordConfirmation = '';
            this.showPassword = false;
            this.passwordCopied = false;
            this.resetModalOpen = true;
        },
        gerarSenha() {
            const prefixos = ['Ceo', 'Saude', 'Odonto', 'Clinica', 'Acesso', 'Sorriso'];
            const randomNum = Math.floor(1000 + Math.random() * 9000);
            const chars = ['@', '#', '$', '!'];
            const char = chars[Math.floor(Math.random() * chars.length)];
            const gerada = prefixos[Math.floor(Math.random() * prefixos.length)] + char + randomNum;
            this.newPassword = gerada;
            this.newPasswordConfirmation = gerada;
            this.showPassword = true;
        },
        copiarSenha() {
            if (!this.newPassword) return;
            navigator.clipboard.writeText(this.newPassword);
            this.passwordCopied = true;
            setTimeout(() => this.passwordCopied = false, 2500);
        }
    }">
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
                                        <div class="flex items-center justify-end gap-1.5">
                                            <!-- Botão Redefinir Senha -->
                                            <button type="button" 
                                                    @click="abrirReset({{ $userItem->id }}, '{{ addslashes($userItem->name) }}', '{{ addslashes($userItem->email) }}')" 
                                                    class="p-1.5 text-amber-600 hover:text-amber-800 hover:bg-amber-50 rounded-lg transition" 
                                                    title="Redefinir Senha do Usuário">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                            </button>

                                            <!-- Botão Editar Dados -->
                                            <a href="{{ route('users.edit', $userItem) }}" class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition" title="Editar Usuário">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>

                                            <!-- Botão Excluir -->
                                            @if($userItem->id !== Auth::id())
                                                <form method="POST" action="{{ route('users.destroy', $userItem) }}" onsubmit="return confirm('Deseja realmente remover este usuário?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition" title="Excluir Usuário">
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

            <!-- Modal de Redefinição de Senha de Usuário pelo Administrador -->
            <div x-show="resetModalOpen" 
                 class="fixed inset-0 z-50 overflow-y-auto" 
                 style="display: none;" 
                 role="dialog" 
                 aria-modal="true">
                <!-- Backdrop -->
                <div x-show="resetModalOpen" 
                     x-transition:enter="ease-out duration-200" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-150" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" 
                     @click="resetModalOpen = false"></div>

                <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="resetModalOpen" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6 border border-slate-200" 
                         @click.outside="resetModalOpen = false">
                        
                        <!-- Cabeçalho do Modal -->
                        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-slate-900">Redefinir Senha de Acesso</h3>
                                <p class="text-xs text-slate-500">Defina uma nova credencial para o colaborador</p>
                            </div>
                            <button type="button" @click="resetModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Card de Identificação do Usuário Alvo -->
                        <div class="my-4 p-3.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-teal-100 text-teal-800 flex items-center justify-center font-bold text-xs border border-teal-200 shrink-0">
                                <span x-text="targetUser.name ? targetUser.name.substring(0, 2).toUpperCase() : 'US'"></span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-slate-800 truncate" x-text="targetUser.name"></p>
                                <p class="text-xs text-slate-500 truncate" x-text="targetUser.email"></p>
                            </div>
                        </div>

                        <!-- Formulário de Redefinição -->
                        <form :action="'{{ url('users') }}/' + targetUser.id + '/reset-password'" method="POST" class="space-y-4">
                            @csrf

                            <!-- Botão de Gerador de Senha Rápida -->
                            <div class="flex items-center justify-between">
                                <label for="modal_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Nova Senha *
                                </label>
                                <button type="button" 
                                        @click="gerarSenha()" 
                                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-700 hover:text-teal-800 bg-teal-50 hover:bg-teal-100 px-2.5 py-1 rounded-lg transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span>Gerar Senha Sugerida</span>
                                </button>
                            </div>

                            <!-- Campo Nova Senha -->
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" 
                                       id="modal_password" 
                                       name="password" 
                                       x-model="newPassword" 
                                       required 
                                       minlength="8" 
                                       placeholder="Mínimo 8 caracteres" 
                                       class="w-full pl-3 pr-20 py-2.5 rounded-xl border border-slate-300 text-sm font-mono focus:border-amber-500 focus:ring-amber-500">
                                
                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-1">
                                    <!-- Botão Copiar Senha -->
                                    <button type="button" 
                                            x-show="newPassword.length > 0" 
                                            @click="copiarSenha()" 
                                            class="p-1 text-slate-400 hover:text-slate-700 rounded transition" 
                                            :title="passwordCopied ? 'Copiada!' : 'Copiar senha'">
                                        <svg x-show="!passwordCopied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                        <svg x-show="passwordCopied" class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>

                                    <!-- Botão Mostrar/Ocultar -->
                                    <button type="button" 
                                            @click="showPassword = !showPassword" 
                                            class="p-1 text-slate-400 hover:text-slate-700 rounded transition" 
                                            :title="showPassword ? 'Ocultar' : 'Mostrar'">
                                        <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Campo Confirmar Senha -->
                            <div>
                                <label for="modal_password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Confirmar Nova Senha *
                                </label>
                                <input :type="showPassword ? 'text' : 'password'" 
                                       id="modal_password_confirmation" 
                                       name="password_confirmation" 
                                       x-model="newPasswordConfirmation" 
                                       required 
                                       minlength="8" 
                                       placeholder="Repita a nova senha" 
                                       class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-mono focus:border-amber-500 focus:ring-amber-500">
                            </div>

                            <!-- Aviso de Segurança -->
                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>A nova senha entrará em vigor imediatamente. Copie ou memorize a senha para repassar com segurança ao colaborador.</span>
                            </div>

                            <!-- Botões de Ação do Modal -->
                            <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                                <button type="button" 
                                        @click="resetModalOpen = false" 
                                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase rounded-xl transition">
                                    Cancelar
                                </button>
                                <button type="submit" 
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold uppercase rounded-xl shadow-md shadow-amber-600/20 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Salvar Nova Senha</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
