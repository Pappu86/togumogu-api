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

class AddArticleDeepLink implements ShouldQueue
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
        $title = strip_tags($this->article->meta_title ?? $this->article->title);
        $description = mb_substr(strip_tags($this->article->meta_description ?? $this->article->excerpt), 0, 300);
        $image = strip_tags($this->article->meta_image ?? $this->article->image);

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

            $this->article->update([
                'longLink' => config('helper.app_url') . '/?link=' . $url . '&apn=com.togumogu&st=' . urlencode($title) . '&sd=' . urlencode($description) . '&si=' . $image,
                'shortLink' => $shortLink,
                'previewLink' => $previewLink,
            ]);
        } else {
            Log::error('dynamicLinkErrorArticle: ' . $response->body());
        }
    }
}
