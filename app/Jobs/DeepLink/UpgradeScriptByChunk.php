<?php

namespace App\Jobs\DeepLink;

use App\Models\Product\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Jobs\DeepLink\AddProductDeepLink;
use App\Jobs\DeepLink\AddProductDeepLinkForFB;
use Illuminate\Support\Facades\DB;

class UpgradeScriptByChunk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * @var
     */
    protected $productsIds;

    /**
     * Create a new job instance.
     *
     * @param $productsIds
     */
    public function __construct($productsIds)
    {
        $this->productsIds = $productsIds;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        // DB::table('products')->whereIn('id', $this->productsIds)
        Product::query()->whereIn('id', $this->productsIds)
            ->get()
            ->map(function ($product) {
                AddProductDeepLink::dispatch($product);
                // AddProductDeepLinkForFB::dispatch($product);
                return $product;
            });
        }
}
