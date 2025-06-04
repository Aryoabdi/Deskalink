<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        if ($user->role !== $role) {
            // If user's role doesn't match, redirect based on their actual role
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard')
                        ->with('error', 'Unauthorized access. Redirected to admin dashboard.');
                case 'partner':
                    return redirect()->route('dashboard')
                        ->with('error', 'Unauthorized access. Redirected to partner dashboard.');
                case 'client':
                    return redirect()->route('dashboard')
                        ->with('error', 'Unauthorized access. Redirected to client dashboard.');
                default:
                    return redirect()->route('dashboard')
                        ->with('error', 'Unauthorized access.');
            }
        }

        return $next($request);
    }
}
