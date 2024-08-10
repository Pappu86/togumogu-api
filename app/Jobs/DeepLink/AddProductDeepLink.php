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

class AddProductDeepLink implements ShouldQueue
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
        $title = strip_tags($this->product->meta_title ?? $this->product->name);
        $description = mb_substr(strip_tags($this->product->meta_description ?? $this->product->excerpt), 0, 300);
        $image = strip_tags($this->product->meta_image ?? $this->product->image);

        $response = Http::asJson()->post('https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=' . config('helper.firebase_api_key'), [
            'dynamicLinkInfo' => [
                'domainUriPrefix' => config('helper.app_url'),
                'link' => $url,
                'androidInfo' => [
                    'androidPackageName' => 'com.togumogu',
                ],
                'iosInfo'=> [
                    'iosBundleId'=> 'com.togumogu-pvt-limited.togumogu'
                ],
                'socialMetaTagInfo' => [
                    'socialTitle' => $title,
                    'socialDescription' => $description,
                    'socialImageLink' => $image,
                ]
            ]
        ]);

        if ($response->successful()) {
            $body = $response->json();

            $shortLink = $body['shortLink'];
            $previewLink = $body['previewLink'];

            $this->product->update([
                'longLink' => config('helper.app_url') . '/?link=' . $url . '&apn=com.togumogu&st=' . urlencode($title) . '&sd=' . urlencode($description) . '&si=' . $image,
                'shortLink' => $shortLink,
                'previewLink' => $previewLink,
            ]);
            Log::info(' deeplink...updated: '. $this->product->id);
        } else {
            Log::error('dynamicLinkErrorProduct: ' . $response->body());
        }
    }
}
