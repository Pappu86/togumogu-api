<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Blog\ArticleApiResource;
use App\Http\Resources\Blog\ArticleAppResource;
use App\Http\Resources\Blog\ArticleSingleApiResource;
use App\Models\Blog\Article;
use App\Models\Blog\Category;
use App\Models\Reward\RewardTransaction;
use App\Traits\ArticleHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class ArticleApiController
 * @package App\Http\Controllers\Blog
 */
class ArticleAppController extends Controller
{
    use ArticleHelpers;

    /**
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     */
    public function getArticles(Request $request, $locale): AnonymousResourceCollection
    {
        App::setLocale($locale);
        $limit = (int)$request->query('limit', 8);
        $sort = $request->query('sort', 'latest');
        $category_slug = $request->query('category');

        $articles = Article::with(['user', 'translations'])
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
            $article_ids = DB::table('blog_article_category_article')
                ->whereIn('category_id', $category_ids)
                ->pluck('article_id')
                ->toArray();
            $articles = $articles->whereIn('id', $article_ids);
        }

        $articles = match ($sort) {
            'featured' => $articles->where('is_featured', '=', 1),
            'trending' => $articles->orderByDesc('view_count'),
            default => $articles->orderByDesc('datetime'),
        };
        $articles = $articles->paginate($limit);
        $articles->appends([
            'limit' => $limit,
            'sort' => $sort,
        ]);

        return ArticleApiResource::collection($articles);
    }

    /**
     * @param $locale
     * @param $slug
     * @return ArticleSingleApiResource
     */
    public function getSingleArticle($locale, $slug): ArticleSingleApiResource
    {
        App::setLocale($locale);

        $article = Article::whereTranslation('slug', $slug)->firstOrFail();
        
        if(isset($article)) {
            $this->updateViewCount($article->id);
        }

        return new ArticleSingleApiResource($article);
    }

    /**
     * @param Request $request
     * @param $locale
     * @param $article_id
     * @param $category_id
     * @return AnonymousResourceCollection
     */
    public function getRelatedArticles(Request $request, $locale, $article_id, $category_id): AnonymousResourceCollection
    {
        App::setLocale($locale);
        $limit = (int)$request->query('limit', 8);

        $articles = Article::with(['user', 'translations'])
            ->where('status', '=', true)
            ->where('category_id', '=', $category_id)
            ->whereNotIn('tracker', ['baby', 'pregnency'])
            ->where('id', '!=', $article_id)
            ->limit($limit)
            ->orderByDesc('datetime')
            ->get();

            $articles = $articles->inRandomOrder();

        return ArticleApiResource::collection($articles);
    }

    /**
     * @param $article_id
     * @return JsonResponse
     */
    public function updateViewCount($article_id): JsonResponse
    {
        DB::beginTransaction();
        try {
            DB::table('blog_articles')
                ->where('id', '=', $article_id)
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
    public function getTrackerArticle(Request $request, $locale): AnonymousResourceCollection
    {
        App::setLocale($locale);
        $limit = (int)$request->query('limit', 1);
        $sort_by = $request->query('sort_by');
        $direction = $request->query('direction', 'asc');

        $articles = Article::with(['user', 'translations'])
            ->where('tracker', '=', $request->get('tracker_type'))
            ->where('tracker_start_day', '<=', $request->get('tracker_day'))
            ->where('tracker_end_day', '>=', $request->get('tracker_day'))
            ->where('status', '=', 1);

        if($articles->count() === 0 && !($request->get('tracker_type') ==='other')) {
            $articles = Article::with(['user', 'translations'])
                ->where('tracker_end_day', '>', $request->get('tracker_day'))
                ->where('tracker', '=', $request->get('tracker_type'))
                ->where('status', '=', 1);
        };
        
        if ($sort_by) {
            $articles = $articles->orderBy($sort_by, $direction);
        }

        $articles = $articles->inRandomOrder()->paginate($limit);

        return ArticleAppResource::collection($articles);
    }

    /**
     * Add reward points after article read.
     *
     * @param Article $article
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function readArticle(Article $article)
    {
        $customer = Auth::user();

        // begin database transaction
        DB::beginTransaction();
        try {

           $existsReward = RewardTransaction::where('category', 'article')
                ->where('action', 'article_read')
                ->where('customer_id', $customer?->id)
                ->where('reference_id', $article?->id)
                ->first();

            if(isset($existsReward)) {
                return response()->json([
                    'message' => "Already, you got reward points of the article!",
                ], 201);
            }

            $rewardPoints = $this->addArticleReadRewardPoints($customer, $article);

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => "Thank you for read the article!",
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
