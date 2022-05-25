<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class isServiceFormation
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


        //Pour protéger les routes pour le service formation
        if (Auth::check()) {
            if (auth()->user()->tokenCan('server:serviceformation')) {
                return $next($request);
            } else {
                return response()->json([

                    'message' => "Accès refusé..Vous n'êtes pas un service formation.",

                ], 403);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Please Login first',

            ]);
        }
    }
}
