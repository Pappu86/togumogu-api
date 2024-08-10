<?php

namespace App\Observers;

use App\Jobs\DeepLink\AddArticleDeepLink;
use App\Jobs\DeepLink\AddArticleDeepLinkForFB;
use App\Traits\CommonHelpers;
use App\Models\Blog\Article;

class ArticleObserver
{

    /**
     * Handle the Article "updating" event.
     *
     * @param Article $article
     * @return void
     */
    public function updating(Article $article)
    {
        $exArticle = Article::find($article->id);
        $commonHelpers = new CommonHelpers;
        if($commonHelpers->isChangedForDynamicLink($article, $exArticle)){
            // generate dynamic link
            AddArticleDeepLink::dispatch($article);
            AddArticleDeepLinkForFB::dispatch($article);
        }

    }

}
