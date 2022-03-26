<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class isChefDepartement
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

        //Pour protéger les routes pour le coordinateur
        if (Auth::check()) {
            if (auth()->user()->tokenCan('server:chefdepartement')) {
                return $next($request);
            } else {
                return response()->json([
                    'status' => 403,
                    'message' => "Accès refusé..Vous n'êtes pas un chef département.",

                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => "S'il vous plait Connectez-vous d'abord",

            ]);
        }
    }
}
