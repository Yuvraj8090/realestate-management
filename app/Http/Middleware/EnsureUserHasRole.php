<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless($user !== null, Response::HTTP_UNAUTHORIZED);

        $allowedRoles = array_map(
            static fn (string $role): string => UserRole::from($role)->value,
            $roles,
        );

        abort_unless(in_array($user->role->value, $allowedRoles, true), Response::HTTP_FORBIDDEN);

        return $next($request);
    }
}
