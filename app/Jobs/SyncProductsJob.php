<?php

namespace App\Jobs;

use App\Enums\SyncLogStatusEnum;
use App\Models\Product;
use App\Models\Shop;
use App\Models\SyncLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use ShopifyService;

class SyncProductsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Shop $shop,
        public ?string $statusFilter = null
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(ShopifyService $svc): void
    {
        $log = SyncLog::create([
            'shop_id' => $this->shop->id,
            'type'    => 'products',
            'status'  => SyncLogStatusEnum::PENDING->value,
            'started_at' => now(),
        ]);

        try {
            foreach ($svc->fetchAllProducts($this->shop, $this->statusFilter) as $p) {
                Product::updateOrCreate(
                    ['shop_id' => $this->shop->id, 'shopify_product_gid' => $p['id']],
                    [
                        'title' => $p['title'],
                        'status' => $p['status'],
                        'handle' => $p['handle'] ?? null,
                        'vendor' => $p['vendor'] ?? null,
                        'product_type' => $p['productType'] ?? null,
                        'tags' => isset($p['tags']) ? implode(',', $p['tags']) : null,
                        'image_url' => $p['featuredImage']['url'] ?? null,
                        'shopify_created_at' => $p['createdAt'] ?? null,
                        'shopify_updated_at' => $p['updatedAt'] ?? null,
                    ]
                );
            }

            $log->update([
                'status' => SyncLogStatusEnum::SUCCESS->value,
                'finished_at' => now(),
                'message' => 'Products synced',
            ]);
        } catch (\Throwable $th) {
            $log->update([
                'status' => SyncLogStatusEnum::FAILED->value,
                'finished_at' => now(),
                'message' => $th->getMessage(),
            ]);
            
            throw $th;
        }
    }
}
