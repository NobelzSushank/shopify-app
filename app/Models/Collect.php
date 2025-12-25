<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Collect extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'collection_id',
        'product_id',
    ];
}
