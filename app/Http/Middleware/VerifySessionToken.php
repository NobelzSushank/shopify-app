<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifySessionToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $auth = $request->header('Authorization');
        abort_unless($auth && str_starts_with($auth, 'Bearer'), Response::HTTP_UNAUTHORIZED, 'Token is missing');

        $token = substr($auth, 7);

        try {
            $decoded = JWT::decode($token, new Key(env('SHOPIFY_API_SECRET'), 'HS256'));
            $request->attributes->set('shop_domain', $decoded->dest);
        } catch (\Throwable $th) {
            abort(Response::HTTP_UNAUTHORIZED, 'Invalid session token');
        }

        return $next($request);
    }
}
