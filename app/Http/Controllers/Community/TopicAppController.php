<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryTreeResource;
use App\Http\Resources\Community\TopicSingleApiResource;
use App\Models\Community\Topic;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Throwable;

class TopicAppController extends Controller
{
    /**
     * @param $locale
     * @return AnonymousResourceCollection
     */
    public function getAll($locale): AnonymousResourceCollection
    {
        App::setLocale($locale);
        $topics = Topic::with('children')
            ->where('status', '=', 'active')
            ->whereIsRoot()->defaultOrder()->get();

        return CategoryTreeResource::collection($topics);
    }

    /**
     * @param $locale
     * @param $slug
     * @return TopicSingleApiResource
     */
    public function getSingleTopic($locale, $slug): TopicSingleApiResource
    {
        App::setLocale($locale);

        $topic = Topic::with('translations')
            ->whereTranslation('slug', $slug)
            ->firstOrFail();

        $ancestors = Topic::with('translations')->ancestorsAndSelf($topic)->pluck('id');
        $descendants = Topic::with('translations')->descendantsAndSelf($topic)->pluck('id');

        // get category ids
        $topic_ids = collect($ancestors)->merge($descendants)->unique()->values()->toArray();
        $posts = DB::table('post_topic_post')
            ->whereIn('topic_id', $topic_ids)
            ->count('post_id');

        if ($topic) {
            $topic->posts_count = $posts;
        }

        return new TopicSingleApiResource($topic);
    }

}
