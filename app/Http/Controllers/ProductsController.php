<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    // public function index(Request $request)
    // {
    //     $shopDomain = $request->attributes->get('shop_domain');
    //     $shop = Shop::where('shop_domain', $shopDomain)->firstOrFail();
    //     $query = Product::where('shop_id', $shop->id);
    //     if ($search = $request->query('q')) {
    //         $query->where('title', 'LIKE', '%'.$search.'%');
    //     }
        
    //     if ($status = $request->query('status')) {
    //         $query->where('status', strtoupper($status));
    //     }
        
    //     $products = $query->orderBy('updated_at_shopify','desc')->paginate(10);
    //     return response()->json($products);
    // }

    public function index(Request $req)
    {
        $shopId = $req->session()->get('shop_id');
        $query = Product::where('shop_id', $shopId);
        if ($search = $req->query('search')) {
            $query->where('title', 'like', "%{$search}%");
        }
        
        if ($status = $req->query('status')) {
            $query->where('status', strtoupper($status));
        }
        
        $pageSize = (int)($req->query('per_page', 10));
        
        $products = $query->orderBy('shopify_updated_at', 'desc')->paginate($pageSize);
        
        return response()->json($products);
    }
}
