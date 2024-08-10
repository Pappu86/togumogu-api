<?php

namespace App\Http\Controllers\Brand;

use App\Models\Brand\Brand;
use App\Models\Brand\BrandTranslation;
use App\Http\Controllers\Controller;
use App\Http\Resources\Brand\BrandAppResource;
use App\Http\Resources\Brand\BrandSingleResource;
use App\Models\Marketing\Offer;
use App\Models\Marketing\Service;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;

class BrandAppController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function getAll(Request $request, $locale)
    {
        App::setLocale($locale);

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);
        $category = $request->query('category');

        $brands = Brand::latest();

        if($category === 'service') {
            $brandIds = Service::where('status', 1)->pluck('brand_id')->toArray();
            $brands = $brands->whereIn('id', $brandIds);
        } else if($category === 'offer') {
            $brandIds = Offer::where('status', 1)->pluck('brand_id')->toArray();
            $brands = $brands->whereIn('id', $brandIds);
        }

        if ($query) {
            $brands = Brand::whereTranslationLike('title', '%' . $query . '%');
        }
        if ($sortBy) {
            $brands = Brand::orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $brands->get();
            $brands = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $brands = $brands->paginate($per_page);
        }
        return BrandAppResource::collection($brands);
    }

    /**
     * @param $locale
     * @param $slug
     * @return BrandSingleResource|JsonResponse
     */
    public function getSingle($locale, $slug): BrandSingleResource|JsonResponse
    {
        App::setLocale($locale);
        // load relations
        $brand = Brand::with(['tags', 'categories'])
            ->whereTranslation('slug', $slug)
            ->first();

        if ($brand) {
            return new BrandSingleResource($brand);
        } else {
            return response()->json([
                'data' => collect()
            ]);
        }
    }
}
