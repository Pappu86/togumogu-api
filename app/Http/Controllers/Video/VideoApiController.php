<?php

namespace App\Http\Controllers\Video;

use App\Http\Controllers\Controller;
use App\Http\Resources\Video\VideoApiCollection;
use App\Http\Resources\Video\VideoApiResource;
use App\Http\Resources\Video\VideoSingleApiResource;
use App\Http\Resources\Product\ProductApiResource;
use App\Models\Video\Video;
use App\Models\Video\Category;
use App\Models\Common\Tag;
use App\Models\Product\Product;
use App\Models\Blog\Article;
use App\Http\Resources\Blog\ArticleApiResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Throwable;

/**
 * Class VideoApiController
 * @package App\Http\Controllers\Video
 */
class VideoApiController extends Controller
{
    /**
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     */
    public function getVideos(Request $request, $locale): AnonymousResourceCollection
    {
        App::setLocale($locale);
        $limit = (int)$request->query('limit', 8);
        $sort = $request->query('sort', 'latest');
        $category_slug = $request->query('category');

        $videos = Video::with(['user', 'translations'])
            ->where('datetime', '<=', now()->toDateTimeString())
            ->whereNotIn('tracker', ['baby', 'pregnency'])
            ->where('status', '=', 1);
        $videos = match ($sort) {
            'featured' => $videos->where('is_featured', '=', 1),
            'trending' => $videos->orderByDesc('view_count'),
            default => $videos->orderByDesc('datetime'),
        };

        // When has category
        if ($request->has('category') && $category_slug !== 'all') {
            $category_id = Category::with('translations')
                ->whereTranslation('slug', $category_slug)
                ->first();

            $ancestors = Category::with('translations')->ancestorsAndSelf($category_id)->pluck('id');
            $descendants = Category::with('translations')->descendantsAndSelf($category_id)->pluck('id');

            // get category ids
            $category_ids = collect($ancestors)->merge($descendants)->unique()->values()->toArray();
            $video_ids = DB::table('video_category_video')
                ->whereIn('category_id', $category_ids)
                ->pluck('video_id')
                ->toArray();
                
            $videos = $videos
                ->whereIn('id', $video_ids);
        }

        $videos = $videos->paginate($limit);
        $videos->appends([
            'limit' => $limit,
            'sort' => $sort,
        ]);

        return VideoApiResource::collection($videos);
    }

    /**
     * @param $locale
     * @param $slug
     * @return VideoSingleApiResource
     */
    public function getSingleVideo($locale, $slug): VideoSingleApiResource
    {
        App::setLocale($locale);

        $video = Video::whereTranslation('slug', $slug)->firstOrFail();

        return new VideoSingleApiResource($video);
    }

    /**
     * @param $locale
     * @param $video_id
     * @return AnonymousResourceCollection
     */
    public function getRelatedVideos($locale, $id):AnonymousResourceCollection
    {
        App::setLocale($locale);

        $tags = Tag::with('videos')
            ->whereHas('videos', function (Builder $query) use ($id) {
                $query->where('taggable_id', '=', $id);
            })
            ->where('status', '=', 'active')
            ->get();

            $video_ids = [];
            // Getting videos ids from tags
            foreach ($tags as $tag) {
                $videos = $tag->videos;
                if($videos) {
                    foreach ($videos as $video) {
                        array_push($video_ids, $video->id);
                    }
                }
            }

            // get unique ids
            $video_ids = collect($video_ids)->unique()->values()->toArray();

            $videos = Video::with('translations')
                ->whereIn('id', $video_ids)
                ->whereNotIn('tracker', ['baby', 'pregnency'])
                ->whereNotIn('id', [$id])
                ->where('status', '=', 1);

                $videos = $videos->paginate(10);

           return VideoApiResource::collection($videos);
    }

    /**
     * @param $locale
     * @param $video_id
     * @return AnonymousResourceCollection
     */
    public function getRelatedProducts($locale, $id):AnonymousResourceCollection
    {
        App::setLocale($locale);

        $tags = Tag::with('products')
            ->whereHas('videos', function (Builder $query) use ($id) {
                $query->where('taggable_id', '=', $id);
            })
            ->where('status', '=', 'active')
            ->get();

            $productIds = [];
            // Getting videos ids from tags
            foreach ($tags as $tag) {
                $products = $tag->products;
                if($products) {
                    foreach ($products as $product) {
                        array_push($productIds, $product->id);
                    }
                }
            }

            // get unique ids
            $productIds = collect($productIds)->unique()->values()->toArray();

            $products = Product::with('translations')
                ->whereIn('id', $productIds)
                ->whereNotIn('id', [$id])
                ->where('status', '=', 1);

                $products = $products->paginate(10);
                
           return ProductApiResource::collection($products);
    }

     /**
     * @param $locale
     * @param $video_id
     * @return AnonymousResourceCollection
     */
    public function getRelatedArticles($locale, $id):AnonymousResourceCollection
    {
        App::setLocale($locale);

        $tags = Tag::with('articles')
            ->whereHas('videos', function (Builder $query) use ($id) {
                $query->where('taggable_id', '=', $id);
            })
            ->where('status', '=', 'active')
            ->get();

            $article_ids = [];
            // Getting videos ids from tags
            foreach ($tags as $tag) {
                $articles = $tag->articles;
                if($articles) {
                    foreach ($articles as $article) {
                        array_push($article_ids, $article->id);
                    }
                }
            }

            // get unique ids
            $article_ids = collect($article_ids)->unique()->values()->toArray();

            $articles = Article::with('translations')
                ->whereIn('id', $article_ids)
                ->whereNotIn('id', [$id])
                ->where('status', '=', 1);

                $articles = $articles->paginate(10);
                
           return ArticleApiResource::collection($articles);
    }

    /**
     * @param $video_id
     * @return JsonResponse
     */
    public function updateViewCount($locale, $video_id): JsonResponse
    {

        DB::beginTransaction();
        try {
            DB::table('videos')
                ->where('id', '=', $video_id)
                ->increment('view_count');

            DB::commit();

            return response()->json([
                'message' => Lang::get('crud.update')
            ]);
        } catch (Throwable $exception) {
            report($exception);
            DB::rollBack();
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }
}
