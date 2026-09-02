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
        $apiKey = $request->header('X-API-Key');

        if (! is_string($apiKey) || ! hash_equals(self::API_KEY, $apiKey)) {
            return response()->json([
                'message' => 'API key tidak valid.',
            ], 401);
        }

        return $next($request);
    }
}
