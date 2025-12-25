<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends Model
{
    use HasUuids;

    protected $fillable = [
        'domain',
        'access_token',
        'scope',
        'installed_at',
    ];

    protected function casts(): array
    {
        return [
            'installed_at' => 'datetime',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    
    public function collections(): HasMany
    {
        return $this->hasMany(Collection::class);
    }
    
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
