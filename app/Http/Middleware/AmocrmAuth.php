<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AmocrmController;
use App\Models\AmocrmToken;
use Closure;
use Illuminate\Http\Request;

class AmocrmAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next)
    {
        $token = AmocrmToken::first();

        if (!$token) {
            return response()->json(['error' => 'There are no valid token'], 401);
        }

        // Check if token already expired
        if (now()->gt($token->updated_at->addSeconds($token->expires_in))) {
            if (!AmocrmController::refreshToken($token)) {
                return response()->json(['error' => 'Can\'t refresh token'], 401);
            }
        }
        $request->merge(['access_token' => $token->access_token]);

        return $next($request);
    }
}
