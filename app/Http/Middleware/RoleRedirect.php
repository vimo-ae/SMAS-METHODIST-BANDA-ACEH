<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $role = Auth::user()->role;

            if ($role === 'siswa') {
                return redirect()->route('siswa.dashboard');
            } elseif ($role === 'guru') {
                return redirect()->route('guru.dashboard');
            } elseif ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
        }

        return $next($request);
    }
}
