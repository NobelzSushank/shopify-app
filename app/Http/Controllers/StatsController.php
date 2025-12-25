<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    // public function overview(Request $request)
    // {
    //     $shopDomain = $request->attributes->get('shop_domain');
    //     $shop = Shop::where('shop_domain', $shopDomain)->firstOrFail();
    //     $totalProducts = Product::where('shop_id',$shop->id)->count();
    //     $collections = Collection::where('shop_id',$shop->id)->get(['id','title','products_count']);
    //     $lastSync = $shop->last_synced_at;
    //     return response()->json([
    //         'totalProducts' => $totalProducts,
    //         'collections' => $collections,
    //         'lastSync' => $lastSync,
    //     ]);
    // }
}
