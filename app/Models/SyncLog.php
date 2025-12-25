<?php

namespace App\Models;

use App\Enums\SyncLogStatusEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'shop_id',
        'type',
        'status',
        'started_at',
        'finished_at',
        'message',
    ];


    protected function casts()
    {
        return [
            'status' => SyncLogStatusEnum::class,
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }
}
