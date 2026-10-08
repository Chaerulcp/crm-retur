<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware pembatas peran.
 *
 * Pemakaian: ->middleware('role:Admin,Gudang')
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        foreach ($roles as $role) {
            if ($user->role === Role::tryFrom($role)) {
                return $next($request);
            }
        }

        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}
