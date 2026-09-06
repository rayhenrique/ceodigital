<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Editar Usuário do Sistema
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $user->name }} ({{ $user->email }})
                </p>
            </div>
            <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-50 transition shadow-xs self-start sm:self-auto">
                Voltar à Lista
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 sm:p-8">
                <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nome Completo *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                        @error('name') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">E-mail Institucional *</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                        @error('email') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="role" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Perfil de Acesso *</label>
                        <select id="role" name="role" required class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                            <option value="operador" {{ old('role', $user->role) === 'operador' ? 'selected' : '' }}>Operador (Atendimento, Agenda, Recepção e Pacientes)</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrador (Acesso Geral, Configurações, Usuários e Auditoria)</option>
                        </select>
                        @error('role') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3" x-data="{
                        editPassword: '',
                        editPasswordConfirmation: '',
                        showEditPassword: false,
                        copied: false,
                        gerarSenhaEdit() {
                            const prefixos = ['Ceo', 'Saude', 'Odonto', 'Clinica', 'Acesso', 'Sorriso'];
                            const randomNum = Math.floor(1000 + Math.random() * 9000);
                            const chars = ['@', '#', '$', '!'];
                            const char = chars[Math.floor(Math.random() * chars.length)];
                            const gerada = prefixos[Math.floor(Math.random() * prefixos.length)] + char + randomNum;
                            this.editPassword = gerada;
                            this.editPasswordConfirmation = gerada;
                            this.showEditPassword = true;
                        },
                        copiarSenhaEdit() {
                            if (!this.editPassword) return;
                            navigator.clipboard.writeText(this.editPassword);
                            this.copied = true;
                            setTimeout(() => this.copied = false, 2500);
                        }
                    }">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs font-bold text-slate-700 uppercase">Redefinir Senha de Acesso (Opcional)</div>
                                <p class="text-xs text-slate-500 mt-0.5">Deixe em branco para manter a senha atual do usuário.</p>
                            </div>
                            <button type="button" 
                                    @click="gerarSenhaEdit()" 
                                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-700 hover:text-teal-800 bg-teal-50 hover:bg-teal-100 border border-teal-200/80 px-2.5 py-1 rounded-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <span>Gerar Senha Sugerida</span>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                            <div>
                                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nova Senha</label>
                                <div class="relative">
                                    <input :type="showEditPassword ? 'text' : 'password'" 
                                           id="password" 
                                           name="password" 
                                           x-model="editPassword" 
                                           minlength="8" 
                                           placeholder="Deixe em branco p/ manter" 
                                           class="w-full pl-3 pr-16 py-2 rounded-lg border-slate-300 text-sm font-mono focus:border-amber-500 focus:ring-amber-500">
                                    
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-1">
                                        <button type="button" 
                                                x-show="editPassword.length > 0" 
                                                @click="copiarSenhaEdit()" 
                                                class="p-1 text-slate-400 hover:text-slate-700 rounded transition" 
                                                :title="copied ? 'Copiada!' : 'Copiar senha'">
                                            <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                            <svg x-show="copied" class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                        <button type="button" 
                                                @click="showEditPassword = !showEditPassword" 
                                                class="p-1 text-slate-400 hover:text-slate-700 rounded transition" 
                                                :title="showEditPassword ? 'Ocultar' : 'Mostrar'">
                                            <svg x-show="!showEditPassword" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <svg x-show="showEditPassword" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                        </button>
                                    </div>
                                </div>
                                @error('password') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Confirmar Nova Senha</label>
                                <input :type="showEditPassword ? 'text' : 'password'" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       x-model="editPasswordConfirmation" 
                                       minlength="8" 
                                       placeholder="Confirme a nova senha" 
                                       class="w-full py-2 px-3 rounded-lg border-slate-300 text-sm font-mono focus:border-amber-500 focus:ring-amber-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" id="status_ativo" name="status_ativo" value="1" {{ old('status_ativo', $user->status_ativo) ? 'checked' : '' }} class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                        <label for="status_ativo" class="text-sm font-semibold text-slate-700">Conta Ativa e Habilitada</label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('users.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-200 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-amber-600 text-white text-sm font-bold rounded-lg hover:bg-amber-700 transition shadow-xs">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
