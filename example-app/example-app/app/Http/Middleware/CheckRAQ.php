<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRAQ
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifie si l'utilisateur est authentifié
        if (Auth::check()) {
            // Vérifie si l'utilisateur a le rôle RAQ
            if (Auth::user()->role !== 'RAQ') {
                return redirect()->route('login'); 
            }
        }
        return $next($request);
        // // Si l'utilisateur n'est pas un RAQ, redirigez-le (par exemple, vers la page d'accueil)
        // return redirect('/')->with('error', "Vous n'avez pas l'autorisation d'accéder à cette page.");
    }
}
