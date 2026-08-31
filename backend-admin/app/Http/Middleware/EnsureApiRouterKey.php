<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiRouterKey
{
    /**
     * Shared router credential. Clients must send:
     * Authorization: Bearer <API_KEY>
     */
    public const API_KEY = 'cHJvZHVjdF9hcHBzX2FwaV9yb3V0ZXJfMjAyNg==';

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! is_string($token) || ! hash_equals(self::API_KEY, $token)) {
            return response()->json([
                'message' => 'API key tidak valid.',
            ], 401);
        }

        // The router key occupies Authorization. Forward the user's Sanctum
        // token from X-Auth-Token for endpoints that also require login.
        $userToken = $request->header('X-Auth-Token');
        if (is_string($userToken) && $userToken !== '') {
            $request->headers->set('Authorization', 'Bearer '.$userToken);
        }

        return $next($request);
    }
}
