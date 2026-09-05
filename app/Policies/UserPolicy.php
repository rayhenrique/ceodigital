<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determina se o usuário pode visualizar a listagem de usuários do sistema.
     * Exclusivo para administradores conforme RBAC.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determina se o usuário pode visualizar um determinado usuário.
     * Administradores podem visualizar qualquer um; operadores apenas o próprio perfil.
     */
    public function view(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->id === $model->id;
    }

    /**
     * Determina se o usuário pode cadastrar novos usuários.
     * Exclusivo para administradores.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determina se o usuário pode atualizar dados de um usuário.
     * Administrador pode editar qualquer usuário; usuário comum apenas o próprio perfil.
     */
    public function update(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->id === $model->id;
    }

    /**
     * Determina se o usuário pode excluir um usuário.
     * Apenas administradores podem excluir, mas nunca a si próprios para evitar lockout.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->isAdmin() && $user->id !== $model->id;
    }

    /**
     * Determina se o usuário pode restaurar o modelo.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determina se o usuário pode excluir permanentemente.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->isAdmin() && $user->id !== $model->id;
    }
}
