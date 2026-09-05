<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Cadastrar Novo Usuário
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Crie um novo acesso para Operador da Recepção ou Administrador do Sistema
                </p>
            </div>
            <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-50 transition shadow-xs">
                Voltar à Lista
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 sm:p-8">
                <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nome Completo *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Ex: Ana Paula Vasconcelos" class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                        @error('name') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">E-mail Institucional *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="usuario@ceodigital.gov.br" class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                        @error('email') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="role" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Perfil de Acesso *</label>
                        <select id="role" name="role" required class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                            <option value="operador" {{ old('role') === 'operador' ? 'selected' : '' }}>Operador (Atendimento, Agenda, Recepção e Pacientes)</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrador (Acesso Geral, Configurações, Usuários e Auditoria)</option>
                        </select>
                        @error('role') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Senha de Acesso *</label>
                            <input type="password" id="password" name="password" required minlength="8" placeholder="Mínimo 8 caracteres" class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                            @error('password') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Confirmar Senha *</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" placeholder="Repita a senha" class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" id="status_ativo" name="status_ativo" value="1" {{ old('status_ativo', true) ? 'checked' : '' }} class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                        <label for="status_ativo" class="text-sm font-semibold text-slate-700">Conta Ativa e Habilitada</label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('users.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-200 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-amber-600 text-white text-sm font-bold rounded-lg hover:bg-amber-700 transition shadow-xs">
                            Cadastrar Usuário
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
