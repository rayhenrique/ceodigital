<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Trata a requisição garantindo que o usuário autenticado esteja ativo e possua perfil admin.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Não autenticado.');
        }

        if (! $user->status_ativo) {
            abort(403, 'Acesso bloqueado. Este usuário está inativo no sistema.');
        }

        if (! $user->isAdmin()) {
            abort(403, 'Acesso negado. Esta área é restrita a administradores do sistema.');
        }

        return $next($request);
    }
}
