<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Http\Resources\Common\FilterTreeResource;
use App\Models\Common\Filter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Models\Product\Category;
use App\Models\Product\Product;
use Illuminate\Support\Facades\Log;

class FilterApiController extends Controller
{
    /**
     * Get all filters.
     *
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     */
    public function getAllFilters($locale, Request $request): AnonymousResourceCollection
    {       
        App::setLocale($locale);
        $categorySlug = $request->query('category');
        $categoryId=null;
        $productIds=[];

        // For all categories
        if(!$categorySlug){
            $productIds = DB::table('products')
            ->pluck('id')
            ->unique()
            ->toArray();   
        }

        // For single category
        if(isset($categorySlug) && $categorySlug !== 'all'){            
            $categoryId = Category::with('translations')
                ->whereTranslation('slug', $categorySlug)
                ->first();
            $ancestors = Category::with('translations')->ancestorsAndSelf($categoryId)->pluck('id');
            $descendants = Category::with('translations')->descendantsAndSelf($categoryId)->pluck('id');

            // get category ids
            $categoryIds = collect($ancestors)->merge($descendants)->unique()->values()->toArray();
            $productIds = DB::table('product_category_product')
                ->whereIn('category_id', $categoryIds)
                ->pluck('product_id')
                ->unique()
                ->toArray();           
        }

        // Get filters 'parent_id' depends on product
        $filterParentIds=[];  
        $filterIds=[];
        if(count($productIds)>0){
            $filterIds=DB::table('product_filter_product')
                ->whereIn('product_id', $productIds)
                ->pluck('filter_id')
                ->unique()
                ->toArray();

            $filterParentIds = DB::table('filters')
                ->whereIn('id', $filterIds)
                ->where('status', '=', 'active')
                ->pluck('parent_id')
                ->unique()
                ->toArray();
        }
        
        $filters = Filter::with(['translations', 'children' => function ($query) {
            $query->with('translations')
                ->defaultOrder()
                ->where('status', '=', 'active');
        }])
        ->whereIn('id', $filterParentIds)
        ->where('status', '=', 'active')
        ->whereIsRoot()->defaultOrder()->get()->map(function($filter) use ($filterIds){
            // Hide all unassign children filter 
            $children=$filter->children;
            $newChildren=array();
            if(count($children)>0){                
                foreach ($children as $child){
                    $isExist = in_array($child->id, $filterIds);
                    if($isExist){
                        array_push($newChildren,$child);
                    }
                }
            }
            if(count($newChildren)>0){
                $filter['children']=$newChildren;
                return $filter;  
            }else{
                return false;
            }                   
        });

        return FilterTreeResource::collection($filters);
    }
}
