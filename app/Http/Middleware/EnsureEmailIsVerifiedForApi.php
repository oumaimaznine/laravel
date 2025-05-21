<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureEmailIsVerifiedForApi
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('api')->user();

        if (! $user || ! $user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Votre adresse email n\'est pas vérifiée.',
            ], 403);
        }

        return $next($request);
    }
}
