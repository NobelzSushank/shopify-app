<?php

namespace App\Http\Controllers;

use App\Enums\ProductStatusEnum;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WebhookController extends Controller
{
    // public function handle(Request $request)
    // {
    //     $topic = $request->header('X-Shopify-Topic');
    //     $shopDomain = $request->header('X-Shopify-Shop-Domain');
    //     $payload = $request->json()->all();
    //     $shop = Shop::where('shop_domain',$shopDomain)->first();
        
    //     if (!$shop) return response()->noContent();
        
    //     if (str_contains($topic, 'create') || str_contains($topic, 'update')) {
    //         $p = $payload;
    //         $product = Product::updateOrCreate(
    //             ['shop_id'=>$shop->id, 'shopify_id'=>$p['id']],
    //             [
    //                 'title'=>$p['title'] ?? '',
    //                 'status'=>strtoupper($p['status'] ?? 'ACTIVE'),
    //                 'vendor'=>$p['vendor'] ?? null,
    //                 'product_type'=>$p['product_type'] ?? null,
    //                 'updated_at_shopify'=>$p['updated_at'] ?? now(),
    //             ]
    //         );
    //     } elseif (str_contains($topic, 'delete')) {
    //         Product::where('shop_id',$shop->id)->where('shopify_id',$payload['id'])->delete();
    //     }
    //     return response()->noContent();
    // }

    public function products(Request $req)
    {
        // Verify HMAC signature
        $hmacHeader = $req->header('X-Shopify-Hmac-SHA256');
        $computed = base64_encode(hash_hmac('sha256', $req->getContent(), env('SHOPIFY_API_SECRET'), true));
        abort_unless(hash_equals($hmacHeader, $computed), Response::HTTP_UNAUTHORIZED, 'Invalid webhook');
        
        $topic = $req->header('X-Shopify-Topic');
        $shopDomain = $req->header('X-Shopify-Shop-Domain');
        $shop = Shop::where('domain', $shopDomain)->first();
        abort_unless($shop, Response::HTTP_NOT_FOUND, 'Shop not found');
        
        $payload = $req->json()->all(); // REST webhook payload
        
        // Minimal mapping for create/update/delete
        if (in_array($topic, ['products/create','products/update'])) {
            Product::updateOrCreate(
                [
                    'shop_id' => $shop->id,
                    'shopify_product_gid' => "gid://shopify/Product/{$payload['id']}"
                ],
                [
                    'title' => $payload['title'] ?? '',
                    'status' => strtoupper($payload['status'] ?? ProductStatusEnum::ACTIVE->value),
                    'handle' => $payload['handle'] ?? null,
                    'vendor' => $payload['vendor'] ?? null,
                    'product_type' => $payload['product_type'] ?? null,
                    'tags' => isset($payload['tags']) ? $payload['tags'] : null,
                    'image_url' => $payload['image']['src'] ?? null,
                    'shopify_created_at' => $payload['created_at'] ?? null,'shopify_updated_at' => $payload['updated_at'] ?? null,
                ]
            );
        } elseif ($topic === 'products/delete') {
            Product::where('shop_id', $shop->id)
            ->where('shopify_product_gid', "gid://shopify/Product/{$payload['id']}")
            ->delete();
        }
        
        return response()->json(['ok' => true]);
    }
    
}
