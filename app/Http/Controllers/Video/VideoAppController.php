<?php

namespace App\Http\Controllers\Video;

use App\Http\Controllers\Controller;
use App\Http\Resources\Video\VideoApiResource;
use App\Http\Resources\Video\VideoAppResource;
use App\Http\Resources\Video\VideoSingleApiResource;
use App\Models\Video\Video;
use App\Models\Video\Category;
use App\Models\Product\Product;
use App\Http\Resources\Product\ProductApiResource;
use App\Models\Blog\Article;
use App\Http\Resources\Blog\ArticleApiResource;
use App\Models\Common\Tag;
use App\Models\Reward\RewardTransaction;
use App\Traits\VideoHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Throwable;

/**
 * Class VideoApiController
 * @package App\Http\Controllers\Blog
 */
class VideoAppController extends Controller
{
    use VideoHelpers;

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
            $videos = $videos->whereIn('id', $video_ids);
        }

        $videos = match ($sort) {
            'featured' => $videos->where('is_featured', '=', 1),
            'trending' => $videos->orderByDesc('view_count'),
            default => $videos->orderByDesc('datetime'),
        };
        $videos = $videos->paginate($limit);
        $videos->appends([
            'limit' => $limit,
            'sort' => $sort,
        ]);

        return VideoAppResource::collection($videos);
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
        
        if(isset($video)) {
            $this->updateViewCount($video->id);
        }

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

                $videos = $videos->inRandomOrder()->paginate(10);

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

            $product_ids = [];
            // Getting videos ids from tags
            foreach ($tags as $tag) {
                $products = $tag->products;
                if($products) {
                    foreach ($products as $product) {
                        array_push($product_ids, $product->id);
                    }
                }
            }

            // get unique ids
            $product_ids = collect($product_ids)->unique()->values()->toArray();

            $products = Product::with('translations')
                ->whereIn('id', $product_ids)
                ->whereNotIn('id', [$id])
                ->where('status', '=', 1);

                $products = $products->inRandomOrder()->paginate(10);
                
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

                $articles = $articles->inRandomOrder()->paginate(10);
                
           return ArticleApiResource::collection($articles);
    }


    /**
     * @param $video_id
     * @return JsonResponse
     */
    public function updateViewCount($video_id): JsonResponse
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

    /**
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     */
    public function getTrackerVideo(Request $request, $locale): AnonymousResourceCollection
    {
        App::setLocale($locale);
        $limit = (int)$request->query('limit', 1);
        $sort_by = $request->query('sort_by');
        $direction = $request->query('direction', 'asc');

        $videos = Video::with(['user', 'translations'])
            ->where('tracker', '=', $request->get('tracker_type'))
            ->where('tracker_start_day', '<=', $request->get('tracker_day'))
            ->where('tracker_end_day', '>=', $request->get('tracker_day'))
            ->where('status', '=', 1);

        if($videos->count() === 0 && !($request->get('tracker_type') ==='other')) {
            $videos = Video::with(['user', 'translations'])
                ->where('tracker_end_day', '>', $request->get('tracker_day'))
                ->where('status', '=', 1);
        };
        
        if ($sort_by) {
            $videos = $videos->orderBy($sort_by, $direction);
        }

        $videos = $videos->inRandomOrder()->paginate($limit);

        return VideoAppResource::collection($videos);
    }

    /**
     * @param Request $request
     * @param $locale
     * @param $type
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function recommendVideosForCustomer(Request $request, $locale)
    {       
        try {
            App::setLocale($locale);
            $customer_id = $request->get('customer_id');
            $running_video_id = $request->get('video_id');
            $limit = $request->get('limit', 5);
            $category_video_ids = [];

            if(!!isset($running_video_id)) {
                $category_ids = DB::table('videos as v')
                    ->join('video_category_video as vcv', 'vcv.video_id', '=', 'v.id')
                    ->join('video_categories as vc', 'vc.id', '=', 'vcv.category_id')
                    ->where('v.id', '=', $running_video_id)
                    ->where('vc.status', '=', 'active')
                    ->pluck('vcv.category_id');
            
                $category_ids = getUniqueArray($category_ids);

                // Get all recommend active categories ids
                $category_video_ids = DB::table('video_category_video')
                    ->whereIn('category_id', $category_ids)
                    ->pluck('video_id');
                $category_video_ids = getUniqueArray($category_video_ids);


                // Get all recommend video ids from active tags 
                $tags = Tag::with('videos')
                ->whereHas('videos', function (Builder $query) use ($running_video_id) {
                    $query->where('taggable_id', '=', $running_video_id);
                })
                ->where('status', '=', 'active')
                ->get();

                $tagable_video_ids = [];
                // Getting videos ids from tags
                foreach ($tags as $tag) {
                    $videos = $tag->videos;
                    if($videos) {
                        foreach ($videos as $video) {
                            array_push($tagable_video_ids, $video->id);
                        }
                    }
                }

                // get unique ids from tags
                $tagable_video_ids =  getUniqueArray($tagable_video_ids);
                $video_ids = array_merge($category_video_ids, $tagable_video_ids);
                $video_ids = getUniqueArray($video_ids);
            }
            
            $videos = Video::query()->where('status','=', '1');

            if ( !!isset($video_ids) && count($video_ids) > 0 ) {
                $recommend_videos = $videos->whereIn('id', $video_ids);
                
                $videos = $recommend_videos->count()>0?$recommend_videos:$videos;           } 
            
            // Avoid opened video in recommend video list
            if(!!isset($running_video_id)) {
                $videos = $videos->whereNotIn('id', [$running_video_id]);
            }

            // Get 5 videos for recommended
            $videos = $videos->inRandomOrder()->paginate($limit);

            // Return actual result
            return VideoAppResource::collection($videos);

        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => $exception->getMessage()
            ], 404);
        }
    }


    /**
     * Add reward points after video watch.
     *
     * @param Video $video
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function watchVideo(Video $video)
    {
        $customer = Auth::user();

        // begin database transaction
        DB::beginTransaction();
        try {

           $existsReward = RewardTransaction::where('category', 'video')
                ->where('action', 'video_watch')
                ->where('customer_id', $customer?->id)
                ->where('reference_id', $video?->id)
                ->first();

            if(isset($existsReward)) {
                return response()->json([
                    'message' => "Already, you got reward points of the video!",
                ], 201);
            }

            $rewardPoints = $this->addVideoWatchRewardPoints($customer, $video);

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => "Thank you for watch the video!",
                'rewardPoints' => $rewardPoints
            ], 200);

        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // rollback database
            DB::rollBack();
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }
}

function getUniqueArray($list) {
    return collect($list)->unique()->values()->toArray();
 }