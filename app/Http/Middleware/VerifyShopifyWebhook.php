<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyShopifyWebhook
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $hmac = $request->header('X-Shopify-Hmac-Sha256');
        $calculated = base64_encode(hash_hmac('sha256', $request->getContent(), env('SHOPIFY_API_SECRET'), true));
        abort_unless(hash_equals($hmac, $calculated), Response::HTTP_UNAUTHORIZED, 'Invalid webhook HMAC');
        return $next($request);
    }
}
