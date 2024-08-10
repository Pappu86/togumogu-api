<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Blog\ArticleApiCollection;
use App\Http\Resources\Blog\ArticleApiResource;
use App\Http\Resources\Blog\ArticleSingleApiResource;
use App\Http\Resources\Product\ProductApiResource;
use App\Models\Blog\Article;
use App\Models\Blog\Category;
use App\Models\Common\Tag;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Throwable;

/**
 * Class ArticleApiController
 * @package App\Http\Controllers\Blog
 */
class ArticleApiController extends Controller
{
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
        $articles = match ($sort) {
            'featured' => $articles->where('is_featured', '=', 1),
            'trending' => $articles->orderByDesc('view_count'),
            default => $articles->orderByDesc('datetime'),
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
            $article_ids = DB::table('blog_article_category_article')
                ->whereIn('category_id', $category_ids)
                ->pluck('article_id')
                ->toArray();
                
            $articles = $articles
                ->whereIn('id', $article_ids);
        }

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

        return new ArticleSingleApiResource($article);
    }

    /**
     * @param $locale
     * @param $article_id
     * @return AnonymousResourceCollection
     */
    public function getRelatedArticles($locale, $id):AnonymousResourceCollection
    {
        App::setLocale($locale);

        $tags = Tag::with('articles')
            ->whereHas('articles', function (Builder $query) use ($id) {
                $query->where('taggable_id', '=', $id);
            })
            ->where('status', '=', 'active')
            ->get();

            $articleIds = [];
            // Getting articles ids from tags
            foreach ($tags as $tag) {
                $articles = $tag->articles;
                if($articles) {
                    foreach ($articles as $article) {
                        array_push($articleIds, $article->id);
                    }
                }
            }

            // get unique ids
            $articleIds = collect($articleIds)->unique()->values()->toArray();

            $articles = Article::with('translations')
                ->whereIn('id', $articleIds)
                ->whereNotIn('tracker', ['baby', 'pregnency'])
                ->whereNotIn('id', [$id])
                ->where('status', '=', 1);

                $articles = $articles->inRandomOrder()->paginate(10);

           return ArticleApiResource::collection($articles);
    }

    /**
     * @param $locale
     * @param $article_id
     * @return AnonymousResourceCollection
     */
    public function getRelatedProducts($locale, $id):AnonymousResourceCollection
    {
        App::setLocale($locale);

        $tags = Tag::with('products')
            ->whereHas('articles', function (Builder $query) use ($id) {
                $query->where('taggable_id', '=', $id);
            })
            ->where('status', '=', 'active')
            ->get();

            $productIds = [];
            // Getting articles ids from tags
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

                $products = $products->inRandomOrder()->paginate(10);
                
           return ProductApiResource::collection($products);
    }

    /**
     * @param $article_id
     * @return JsonResponse
     */
    public function updateViewCount($locale, $article_id): JsonResponse
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
}
