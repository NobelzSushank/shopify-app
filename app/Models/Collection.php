<?php

namespace App\Models;

use App\Enums\CollectionTypeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'shop_id',
        'shopify_collection_gid',
        'title',
        'type',
        'products_count',
    ];

    protected function casts(): array
    {
        return [
            'type' => CollectionTypeEnum::class,
            'products_count' => 'integer',
        ];
    }
}
