<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'shop_id',
        'shopify_order_gid',
        'name',
        'financial_status',
        'fulfillment_status',
        'total_price',
        'currency',
        'shopify_created_at',
        'shopify_updated_at',
    ];

    protected function casts()
    {
        return [
            'total_price' => 'decimal:2',
            'shopify_created_at' => 'datetime',
            'shopify_updated_at' => 'datetime',
        ];
    }
}
