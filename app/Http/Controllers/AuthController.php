<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // public function install(Request $request): RedirectResponse
    // {
    //     $shop = trim($request->query('shop'));
    //     abort_unless(
    //         $shop,
    //         Response::HTTP_BAD_REQUEST,
    //         'Shop is missing.'
    //     );

    //     $scopes = 'read_products, read_orders';
    //     $redirectUri = config('app.url').'/api/auth/callback';
    //     $randomStr = Str::random(16);
    //     $installUrl = 'https://{$shop}/admin/oauth/authorize?' . http_build_query([
    //         'client_id' => env('SHOPIFY_API_KEY'),
    //         'scope' => $scopes,
    //         'redirect_uri' => $redirectUri,
    //         'state' => $randomStr,
    //         'grant_options[]' => 'per-user'
    //     ]);

    //     return redirect($installUrl);
    // }

    // public function callback(Request $request)
    // {
    //     $shop = $request->query('shop');
    //     $code = $request->query('code');
    //     $hmac = $request->query('hmac');

    //     //validate hmac
    //     $params = $request->query();
    //     unset($params['hmac']);
    //     ksort($params);

    //     $computed = hash_hmac('sha256', http_build_query($params), env('SHOPIFY_API_SECRET'));

    //     abort_unless(hash_equals($computed, $hmac), Response::HTTP_UNAUTHORIZED, 'Invalid HMAC');

    //     // Exchange Code
    //     $resp = Http::asForm()->post("https://{$shop}/admin/oauth/access_token", [
    //         'client_id' => env('SHOPIFY_API_KEY'),
    //         'client_secret' => env('SHOPIFY_API_SECRET'),
    //         'code' => $code,
    //     ])->json();

    //     $accessToken = $resp['access_token'] ?? null;
    //     abort_unless($accessToken, Response::HTTP_INTERNAL_SERVER_ERROR, 'Token Exchange failed');

    //     $shopModel = Shop::updateOrCreate(
    //         ['shop_domain' => $shop],
    //         ['access_token' => $accessToken]
    //     );

    //     // Register Webhooks
    //     // app(ShopifyService::class)->registerProductWebhooks($shopModel);

    //     // Redirect to embedded app index with host param
    //     $host = $request->query('host');
    //     return redirect(env('FRONTEND_URL')."/?shop={$shop}&host{$host}");


    // }


    public function install(Request $req)
    {
        $host = $req->query('host') ?? $req->session()->get('host');
        $shop = $req->query('shop') ?? $req->session()->get('shop');

        if ($host) { $req->session()->put('host', $host); }
        if ($shop) { $req->session()->put('shop', $shop); }
        
        if (blank($shop)) { $shop = "ecommerce-1234700.myshopify.com"; }
        if (blank($host)) { $host = "YWRtaW4uc2hvcGlmeS5jb20vc3RvcmUvZWNvbW1lcmNlLTEyMzQ3MDA"; }
        
        abort_unless($shop && str_ends_with($shop, '.myshopify.com'), 400, 'Invalid shop');
        
        $state = Str::random(16);
        $req->session()->put('state', $state);
        $params = http_build_query([
            'client_id'    => config('services.shopify.key'),
            'scope'        => env('SHOPIFY_SCOPES'),
            'redirect_uri' => route('oauth.callback'),
            'state'        => $state,
        ]);
        
        return redirect("https://{$shop}/admin/oauth/authorize?{$params}");
    }
    
    public function callback(Request $req): RedirectResponse
    {
        // $shop   = $req->query('shop');
        $code   = $req->query('code');
        $state  = $req->query('state');

        $host = $req->query('host') ?? $req->session()->get('host');
        $shop = $req->query('shop') ?? $req->session()->get('shop');
        Log::info("code and state");
        Log::info($code);
        Log::info($state);
        Log::info($shop);
        
        // abort_unless($shop && $code && $state === $req->session()->get('state'), 400, 'Invalid OAuth');        // Exchange code for access token
        abort_unless($shop && $code && $state, 400, 'Invalid OAuth');
        $client = new Client();
        $resp = $client->post("https://{$shop}/admin/oauth/access_token", [
            'json' => [
                'client_id'     => env('SHOPIFY_API_KEY'),
                'client_secret' => env('SHOPIFY_API_SECRET'),
                'code'          => $code,
            ],
        ]);
        
        $data = json_decode((string)$resp->getBody(), true);
        $token = $data['access_token'] ?? null;
        abort_unless($token, 500, 'No token');
        $shopModel = Shop::updateOrCreate(
            ['domain' => $shop],
            ['access_token' => $token, 'scope' => env('SHOPIFY_SCOPES'), 'installed_at' => now()]
        );        // Set session for backend API
        
        $req->session()->put('shop_id', $shopModel->id);        // Redirect to embedded app route (frontend served here)

        // Build the minimal query App Bridge needs.
        $params = [];
        if ($host) $params['host'] = $host;
        if ($shop) $params['shop'] = $shop;
        $qs = $params ? ('?'.http_build_query($params)) : '';

        Log::info('QSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQS-------');
        Log::info($qs);
        // Redirect to your embedded SPA entry (Laravel or Vite dev server in local)
        return redirect()->away('https://shopify-dash.sushankpokharel.com.np/'.$qs);
        if (app()->environment('local')) {        return redirect()->away('http://localhost:5173/'.$qs);    }
        return redirect()->route('embedded')->with(['shop' => $shop]);
    }
    
    public function embedded(Request $req)
    {
        // Serve the frontend index.html (Vite built)
        if (app()->environment('local')) {
            $qs = request()->getQueryString();
            Log::info('QSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQS');
            Log::info($qs);
            // Proxy to Vite dev server (default 5173)
            return redirect()->away('http://localhost:5173' . ($qs ? ('/?'.$qs) : '/'));
        }

        return response()->file(public_path('index.html'));
    }

    protected function appendQuery(): string
    {
        // Preserve host/shop params required by App Bridge
        $qs = request()->getQueryString();
        Log::info('QSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQSQS');
        Log::info($qs);
        return $qs ? ('/?'.$qs) : '/';
    }
}
