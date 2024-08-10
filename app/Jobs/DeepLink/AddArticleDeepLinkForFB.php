<?php

namespace App\Jobs\DeepLink;

use App\Models\Blog\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AddArticleDeepLinkForFB implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Article
     */
    protected Article $article;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $url = config('helper.url') . '/articles/' . $this->article->slug;
       
        $response = Http::asJson()->post('https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=' . config('helper.firebase_api_key'), [
            'dynamicLinkInfo' => [
                'domainUriPrefix' => config('helper.app_url'),
                'link' => $url,
                'androidInfo' => [
                    'androidPackageName' => 'com.togumogu',
                ],
                'iosInfo'=> [
                    'iosBundleId'=> 'com.togumogu-pvt-limited.togumogu'
                ]
            ]
        ]);
        if ($response->successful()) {
            $body = $response->json();
            $facebookLink = $body['shortLink'];

            $this->article->update([
                'facebookLink' => $facebookLink,
            ]);
        } else {
            Log::error('dynamicLinkErrorArticle: ' . $response->body());
        }
    }
}
