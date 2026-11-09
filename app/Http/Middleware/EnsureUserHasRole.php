<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Batasi akses berdasarkan satu atau beberapa peran.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        $allowedRoles = array_filter(array_map(
            static fn (string $role): ?UserRole => UserRole::tryFrom($role),
            $roles
        ));

        if (! in_array($user->role, $allowedRoles, true)) {
            abort(403, __('messages.access_denied'));
        }

        return $next($request);
    }
}
