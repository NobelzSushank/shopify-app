<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Product;
use App\Models\SyncLog;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function index(Request $req)
    {
        $shopId = $req->session()->get('shop_id');
        $productsCount = Product::where('shop_id', $shopId)->count();
        $collectionsCount = Collection::where('shop_id', $shopId)->count();
        $lastSync = SyncLog::where('shop_id',$shopId)->where('type','products')->orderBy('finished_at','desc')->first();
        return response()->json([
            'products' => $productsCount,
            'collections' => $collectionsCount,
            'lastSyncTime' => $lastSync?->finished_at,
        ]);
    }
}
