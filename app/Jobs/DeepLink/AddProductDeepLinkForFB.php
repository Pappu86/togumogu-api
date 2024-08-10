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

class AddProductDeepLinkForFB implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Product
     */
    protected Product $product;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $url = config('helper.url') . '/products/' . $this->product->slug;
        $this->product->update([
            'facebookLink' => $url,
        ]);
        
        Log::info(' facebookLink...updated: '. $this->product->id);
        // $response = Http::asJson()->post('https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=' . config('helper.firebase_api_key'), [
        //     'dynamicLinkInfo' => [
        //         'domainUriPrefix' => config('helper.app_url'),
        //         'link' => $url,
        //         'androidInfo' => [
        //             'androidPackageName' => 'com.togumogu',
        //         ],
        //         'iosInfo'=> [
        //             'iosBundleId'=> 'com.togumogu-pvt-limited.togumogu'
        //         ]
        //     ]
        // ]);

        Log::info(' facebookLink...updated: '. $this->product->id);
        // $response = Http::asJson()->post('https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=' . config('helper.firebase_api_key'), [
        //     'dynamicLinkInfo' => [
        //         'domainUriPrefix' => config('helper.app_url'),
        //         'link' => $url,
        //         'androidInfo' => [
        //             'androidPackageName' => 'com.togumogu',
        //         ],
        //         'iosInfo'=> [
        //             'iosBundleId'=> 'com.togumogu-pvt-limited.togumogu'
        //         ]
        //     ]
        // ]);

        // if ($response->successful()) {
        //     $body = $response->json();
        //     $facebookLink = $body['shortLink'];

        //     $this->product->update([
        //         'facebookLink' => $facebookLink,
        //     ]);
        // } else {
        //     Log::error('dynamicLinkErrorProduct: ' . $response->body());
        // }
    }
}
