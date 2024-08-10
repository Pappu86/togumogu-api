<?php

namespace App\Http\Controllers;

use App\Http\Resources\Blog\ArticleApiResource;
use App\Models\Daycare\Daycare;
use App\Http\Resources\Daycare\DaycareApiResource;
use App\Http\Resources\Product\ProductApiResource;
use App\Http\Resources\Video\VideoApiResource;
use App\Models\Blog\Article;
use App\Models\Product\Product;
use App\Models\Video\Video;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    /**
     * @param Request $request
     * @param $locale
     */
    public function search(Request $request, $locale)
    {
        App::setLocale($locale);

        $type = $request->query('type', 'product');
        $query = $request->query('query');
        $limit = $request->query('limit', 20);
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');

        if($type === 'product') {
            return ProductApiResource::collection($this->getProducts($request, $query, $limit, $sortBy, $direction));
        } if($type === 'article') {
            return ArticleApiResource::collection($this->getArticles($request, $query, $limit, $sortBy, $direction));
        } if($type === 'daycare') {
            return DaycareApiResource::collection($this->getDayCares($request, $query, $limit, $sortBy, $direction, $locale));
        } if($type === 'video') {
            return VideoApiResource::collection($this->getVideos($request, $query, $limit, $sortBy, $direction));
        }
    }

    /**
     * @param $request
     * @param $query
     * @param $limit
     * @param $sortBy
     * @param $direction
     * @return mixed
     */
    private function getProducts($request, $query, $limit, $sortBy, $direction): mixed
    {
        $filters = $request->query('filters');
        $products = Product::with(['translations'])
            ->where('status', '=', 1)
            ->where('approved_status', '=', 'approved')
            ->where('datetime', '<=', now()->toDateTimeString())
            ->whereTranslationLike('name', '%' . $query . '%')
            ->whereTranslationLike('excerpt', '%' . $query . '%');

        // filter products
        if ($request->filled('filters')) {
            $filter_ids = explode(',', $request->query('filters'));
            $filters = DB::table('filters')
            ->whereIn('id', $filter_ids)
            ->where('status', '=', 'active')
            ->get();

            $prevParentId='';
            $allProductIds=array();
            $isAnotherParent=false;
            foreach ($filters as $index=>$filter) {
                $index+=1;
                $filterId=$filter->id;
                $currentParentId=$filter->parent_id;
                $filterProductIds = DB::table('product_filter_product')
                    ->where('filter_id', $filterId)
                    ->pluck('product_id')
                    ->unique()
                    ->toArray();

                if($index===1){
                    $allProductIds=$filterProductIds;
                }else{
                    if($prevParentId !== $currentParentId){
                        $isAnotherParent=true;
                    }
                    if($isAnotherParent){
                        $allProductIds = collect(array_intersect($allProductIds, $filterProductIds))->unique()->values()->toArray();
                    }else{
                        $allProductIds = collect($allProductIds)->merge($filterProductIds)->unique()->values()->toArray();
                    }
                }
                $prevParentId=$filter->parent_id;
            }
            $products = $products->whereIn('id', $allProductIds);
        }

        // filter by price
        // if ($request->has('price')) {
        //     $price = explode(',', $request->query('price'));
        //     $products = $products->whereBetween('price', $price);
        // }

        if ($sortBy) {
            $products = $products->orderBy($sortBy, $direction);
        }

        if ($limit === '-1') {
            $results = $products->get();
            $products = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $products = $products->paginate($limit);
        }

        return $products;
    }

    /**
     * @param $request
     * @param $query
     * @param $limit
     * @param $sortBy
     * @param $direction
     * @return mixed
     */
    private function getArticles($request, $query, $limit, $sortBy, $direction): mixed
    {
        $articles = Article::with(['user', 'translations'])
            ->where('datetime', '<=', now()->toDateTimeString())
            ->where('status', '=', 1)
            ->whereTranslationLike('title', '%' . $query . '%')
            ->orWhereTranslationLike('excerpt', '%' . $query . '%');
            
        $articles = $articles->where('status', '=', 1);

        if ($sortBy) {
            $articles = $articles->orderBy($sortBy, $direction);
        }

        if ($limit === '-1') {
            $results = $articles->get();
            $articles = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $articles = $articles->paginate($limit);
        }

        return $articles;
    }

    /**
     * @param $request
     * @param $query
     * @param $limit
     * @param $sortBy
     * @param $direction
     * @param $locale
     * @return mixed
     */
    private function getDayCares($request, $query, $limit, $sortBy, $direction, $locale): mixed
    {
        $daycares = Daycare::with('translations')
                ->where('status', '=', 'active')
                ->whereTranslation('locale', $locale)
                ->whereTranslationLike('name', '%' . $query . '%')
                ->orWhereTranslationLike('description', '%' . $query . '%');
        
        $daycares = $daycares->where('status', '=', 1);
                
        if ($sortBy) {
            $daycares = $daycares->orderBy($sortBy, $direction);
        }

        if ($limit === '-1') {
            $results = $daycares->get();
            $daycares = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $daycares = $daycares->paginate($limit);
        }

        return $daycares;
        
    }

    /**
     * @param $request
     * @param $query
     * @param $limit
     * @param $sortBy
     * @param $direction
     * @return mixed
     */
    private function getVideos($request, $query, $limit, $sortBy, $direction): mixed
    {
        $videos = Video::with(['user', 'translations'])
            ->where('datetime', '<=', now()->toDateTimeString())
            ->where('status', '=', 1)
            ->whereTranslationLike('title', '%' . $query . '%')
            ->orWhereTranslationLike('sub_title', '%' . $query . '%')
            ->orWhereTranslationLike('excerpt', '%' . $query . '%');

        $videos = $videos->where('status', '=', 1);

        if ($sortBy) {
            $videos = $videos->orderBy($sortBy, $direction);
        }

        if ($limit === '-1') {
            $results = $videos->get();
            $videos = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $videos = $videos->paginate($limit);
        }

        return $videos;
    }

}
