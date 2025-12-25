<?php

namespace App\Http\Controllers;

use App\Jobs\SyncProductsJob;
use App\Models\Shop;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    // public function products(Request $request)
    // {
    //     $shopDomain = $request->attributes->get('shop_domain');
    //     $shop = Shop::where('shop_domain', $shopDomain)->firstOrFail();
    //     $svc = app(ShopifyService::class);
    //     $log = SyncLog::create([
    //         'shop_id' => $shop->id,
    //         'resource' => 'products',
    //         'started_at' => now(),
    //     ]);
        
    //     $cursor = null;
    //     $created = $updated = $fetched = 0;
        
    //     do {
    //         $resp = $svc->fetchProducts($shop, $cursor, null);
    //         $edges = data_get($resp, 'data.products.edges', []);
    //         $hasNext = data_get($resp, 'data.products.pageInfo.hasNextPage', false);
            
    //         foreach ($edges as $edge) {
    //             $fetched++;
    //             $p = $edge['node'];
    //             [$gid] = [$p['id']];
    //             $shopifyId = Str::after($gid, 'Product/');
    //             $product = Product::updateOrCreate(
    //                 ['shop_id'=>$shop->id, 'shopify_id'=>$shopifyId],
    //                 [
    //                     'title'=>$p['title'],
    //                     'status'=>$p['status'],
    //                     'vendor'=>$p['vendor'] ?? null,
    //                     'product_type'=>$p['productType'] ?? null,
    //                     'updated_at_shopify'=>$p['updatedAt'],
    //                 ]
    //             );
                
    //             $product->variants()->delete();
                
    //             foreach (data_get($p,'variants.edges',[]) as $ve) {
    //                 $v = $ve['node'];
    //                 $product->variants()->create([
    //                     'shopify_id'=>Str::after($v['id'],'ProductVariant/'),
    //                     'title'=>$v['title'],
    //                     'sku'=>$v['sku'] ?? null,
    //                     'price'=>$v['price'] ?? null,
    //                     'inventory_quantity'=>$v['inventoryQuantity'] ?? null,
    //                 ]);
    //             }
                
    //             $product->images()->delete();
                
    //             foreach (data_get($p,'images.edges',[]) as $ie) {
    //                 $img = $ie['node'];
    //                 $product->images()->create([
    //                     'shopify_id'=>Str::after($img['id'],'ProductImage/'),
    //                     'src'=>$img['originalSrc'],
    //                     'position'=>null,
    //                 ]);
    //             }
    //         }
            
    //         $cursor = count($edges) ? end($edges)['cursor'] : null;
    //     } while ($hasNext);
        
    //     $shop->last_synced_at = now();
    //     $shop->save();
    //     $log->update([
    //         'fetched'=>$fetched,
    //         'created'=>$created, // if you track new vs update
    //         'updated'=>$updated,
    //         'finished_at'=>now(),
    //         'status'=>'SUCCESS'
    //     ]);
        
    //     return response()->json(['message' => 'Products synced', 'fetched' => $fetched]);
    // }

    public function products(Request $req)
    {
        $shop = Shop::findOrFail($req->session()->get('shop_id'));
        dispatch(new SyncProductsJob($shop));
        return response()->json(['queued' => true]);
    }
}
