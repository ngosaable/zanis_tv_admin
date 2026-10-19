<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Only staff roles may open the /admin panel.
 * Subscribers (people who registered to watch) are sent back to the public site.
 */
class EnsureUserIsAdmin
{
    /**
     * Roles allowed into the admin panel.
     * "admin" is kept for accounts created before roles were introduced
     * (the users.role column used to default to "admin").
     */
    public const ADMIN_ROLES = ['super_admin', 'content_manager', 'admin'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->role, self::ADMIN_ROLES, true) || $user->is_active === false) {
            if ($request->expectsJson()) {
                abort(403, 'You do not have access to the admin panel.');
            }

            auth()->guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'This account does not have admin access.']);
        }

        return $next($request);
    }
}
