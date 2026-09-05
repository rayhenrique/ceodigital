<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Listagem de operadores e administradores do sistema (Exclusivo Admin).
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $busca = $request->input('busca');
        $role = $request->input('role');

        $users = User::query()
            ->when($busca, fn ($q) => $q->where('name', 'like', "%{$busca}%")->orWhere('email', 'like', "%{$busca}%"))
            ->when($role, fn ($q) => $q->where('role', $role))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users', 'busca', 'role'));
    }

    /**
     * Formulário de cadastro de novo usuário.
     */
    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('users.create');
    }

    /**
     * Cadastra novo operador ou administrador.
     */
    public function store(UserFormRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $dados = $request->validated();
        $dados['password'] = Hash::make($dados['password']);
        $dados['status_ativo'] = $request->boolean('status_ativo', true);

        User::create($dados);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuário cadastrado com sucesso.');
    }

    /**
     * Formulário de edição de usuário e redefinição de perfil/senha.
     */
    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    /**
     * Atualiza dados de acesso, perfil ou senha.
     */
    public function update(UserFormRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $dados = $request->safe()->except('password');
        $dados['status_ativo'] = $request->boolean('status_ativo');

        if ($request->filled('password')) {
            $dados['password'] = Hash::make($request->input('password'));
        }

        $user->update($dados);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    /**
     * Remove um usuário do sistema, impedindo auto-exclusão.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Você não pode excluir sua própria conta de administrador.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuário removido com sucesso.');
    }
}
