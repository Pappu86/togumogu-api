<?php

namespace App\Http\Controllers\Product;

use App\Http\Resources\Product\ProductApiResource;
use App\Http\Resources\Blog\ArticleApiResource;
use App\Models\Product\Category;
use App\Models\Product\Product;
use App\Models\Common\Tag;
use App\Models\Blog\Article;
use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductFeaturedApiResource;
use App\Http\Resources\Product\ProductSingleApiResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class ProductAppController extends Controller
{
    /**
     * @param $locale
     * @param $slug
     * @return ProductSingleApiResource|JsonResponse
     */
    public function getProductBySlug($locale, $slug): JsonResponse|ProductSingleApiResource
    {
        App::setLocale($locale);

        $product = Product::with('translations', 'categories', 'images', 'tabs', 'tags')
            ->whereTranslation('slug', $slug)
            ->first();

        if ($product) {
            return new ProductSingleApiResource($product);
        } else {
            return response()->json(collect());
        }
    }

    /**
     * Get products by pagination.
     *
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function getProducts(Request $request, $locale): AnonymousResourceCollection|JsonResponse
    {
        try {
            App::setLocale($locale);
            $limit = (int)$request->query('limit', 8);
            $sort = $request->query('sort', 'latest');

            $category_slug = $request->query('category');

            $products = Product::with(['translations'])
                ->where('status', '=', 1)
                ->where('approved_status', '=', 'approved')
                ->where('datetime', '<=', now()->toDateTimeString());

            if ($request->has('category') && $category_slug !== 'all') {
                $category_id = Category::with('translations')
                    ->whereTranslation('slug', $category_slug)
                    ->first();

                $ancestors = Category::with('translations')->ancestorsAndSelf($category_id)->pluck('id');
                $descendants = Category::with('translations')->descendantsAndSelf($category_id)->pluck('id');

                // get category ids
                $category_ids = collect($ancestors)->merge($descendants)->unique()->values()->toArray();
                $product_ids = DB::table('product_category_product')
                    ->whereIn('category_id', $category_ids)
                    ->pluck('product_id')
                    ->toArray();
                $products = $products
                    ->whereIn('id', $product_ids);
            }

            // filter products
            if ($request->filled('filters')) {
                $filter_ids = explode(',', $request->query('filters'));
                $product_ids = DB::table('product_filter_product')
                    ->whereIn('filter_id', $filter_ids)
                    ->pluck('product_id')
                    ->toArray();
                $products = $products
                    ->whereIn('id', $product_ids);
            }

            // filter by price
            // $price = (int)$request->query('price');
            // if ($request->has('price')) {
            //     $price = explode(',', $request->query('price'));
            //     $products = $products->whereBetween('price', $price);
            // }

            $products = match ($sort) {
                'featured' => $products->where('is_featured', '=', true),
                'bestsellers' => $products->orderByDesc('sales_count'),
                'low_to_high' => $products->orderBy('price', 'asc'),                
                'high_to_low' => $products->orderByDesc('price'),
                default => $products->orderByDesc('datetime'),
            };
            $products = $products->paginate($limit);
            $products->appends([
                'limit' => $limit,
                'sort' => $sort,
            ]);

            // filter by price high-low and low-high
            if($sort==='low_to_high' || $sort==='high_to_low'){
                $products->each(function ($product) {
                    $price=$product->price;
                    $filterPrice=$product->price;
                    $specialPrice=$product->special_price;
                    if(isset($specialPrice)){
                        $filterPrice=now()->betweenIncluded($product->special_start_date, $product->special_end_date) ? (float)$specialPrice:$price;
                    }
                    $product->filter_price = $filterPrice;   
                });

                if($sort==='low_to_high'){
                    $productsWithCustomPro = $products->getCollection()->sortBy('filter_price');
                }else{
                    $productsWithCustomPro = $products->getCollection()->sortBy('filter_price', 1, true);
                }
                $products->setCollection(collect($productsWithCustomPro));
            }

            return ProductApiResource::collection($products);

        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => $exception->getMessage()
//                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     */
    public function getTrackerProduct(Request $request, $locale): AnonymousResourceCollection
    {
        App::setLocale($locale);
        $limit = (int)$request->query('limit', 1);
        $sort_by = $request->query('sort_by');
        $direction = $request->query('direction', 'asc');

        $products = Product::with(['translations'])
            ->where('tracker', '=', $request->get('tracker_type'))
            ->where('tracker_start_day', '<=', $request->get('tracker_day'))
            ->where('tracker_end_day', '>=', $request->get('tracker_day'))
            ->where('status', '=', 1);

        if($products->count() === 0 && !($request->get('tracker_type') ==='other')) {
            $products = Product::with(['translations'])
                ->where('tracker_end_day', '>', $request->get('tracker_day'))
                ->where('status', '=', 1);
        };
        
        if ($sort_by) {
            $products = $products->orderBy($sort_by, $direction);
        }

        $products = $products->inRandomOrder()->paginate($limit);

       return ProductApiResource::collection($products);
    }

    /**
     * @param $locale
     * @param $product_id
     * @return AnonymousResourceCollection
     */
    public function getRelatedArticles($locale, $id):AnonymousResourceCollection
    {
        App::setLocale($locale);

        $tags = Tag::with('articles')
            ->whereHas('products', function (Builder $query) use ($id) {
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
                ->whereNotIn('id', [$id])
                ->where('status', '=', 1);

                $articles = $articles->inRandomOrder()->paginate(6);

           return ArticleApiResource::collection($articles);
    }

    /**
     * @param $locale
     * @param $product_id
     * @return AnonymousResourceCollection
     */
    public function getRelatedProducts($locale, $id):AnonymousResourceCollection
    {
        App::setLocale($locale);

        $tags = Tag::with('products')
            ->whereHas('products', function (Builder $query) use ($id) {
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

                $products = $products->inRandomOrder()->paginate(5);
                
           return ProductApiResource::collection($products);
    }

}
