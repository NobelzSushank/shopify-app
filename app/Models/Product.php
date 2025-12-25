<?php

namespace App\Models;

use App\Enums\ProductStatusEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'shop_id',
        'shopify_product_gid',
        'title',
        'status',
        'handle',
        'vendor',
        'product_type',
        'tags',
        'image_url',
        'shopify_created_at',
        'shopify_updated_at',
    ];

    protected function casts(): array
    {
        return [
            'shopify_created_at' => 'datetime',
            'shopify_updated_at' => 'datetime',
            'status' => ProductStatusEnum::getAllValues(),
        ];
    }
}
