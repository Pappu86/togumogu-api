<?php

namespace App\Http\Controllers\Daycare;

use App\Http\Controllers\Controller;
use App\Http\Resources\Daycare\DaycareApiResource;
use App\Http\Resources\Daycare\DaycareSingleApiResource;
use App\Models\Daycare\Daycare;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class DaycareAppController extends Controller
{
    /**
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     */
    public function getDayCares(Request $request, $locale): AnonymousResourceCollection
    {
        App::setLocale($locale);

        $limit = (int)$request->query('limit', 12);
        $featured = (bool)$request->query('featured', false);
        $latitude = $request->query('latitude');
        $longitude = $request->query('longitude');
        $categories = $request->query('categories');

        $collections = Daycare::with('translations')
        ->where('status', '=', 'active');

        if ($featured) {
            $collections = $collections->where('is_featured', '=', 1);
        }

        if ($request->filled('categories')) {
            $ids = explode(',', $categories);
            $daycare_ids = DB::table('daycare_category_daycare')
            ->whereIn('daycare_category_id', $ids)
            ->pluck('daycare_id');

            $collections = $collections->whereIn('id', $daycare_ids);
        }

        if (isset($latitude) && isset($longitude)) {
            $collections = $collections->select()
            ->addSelect(
                DB::raw('6371 * acos(cos(radians(' . $latitude . ')) * cos(radians(latitude)) * cos(radians(longitude) - radians(' . $longitude . ')) + sin(radians(' . $latitude . ')) * sin(radians(latitude))) as distance')
            )
            ->having('distance', '<', 15)
            ->orderBy('distance', 'asc');
        }

        $collections = $collections->paginate($limit);
        $collections->appends([
            'limit' => $limit,
            'categories' => $categories,
            'featured' => $featured
        ]);

        return DaycareApiResource::collection($collections);
    }

    /**
     * @param $locale
     * @return JsonResponse
     */
    public function getDayCareCategories($locale): JsonResponse
    {
        $categories = DB::table('daycare_categories as c')
            ->join('daycare_category_translations as ct', 'c.id', '=', 'ct.daycare_category_id')
            ->select('c.id', 'ct.name', 'c.image', 'ct.slug', 'ct.description')
            ->where('c.status', '=', 'active')
            ->where('ct.locale', '=', $locale)
            ->get();

        return response()->json([
            'data' => $categories
        ]);
    }

    /**
     * @param $locale
     * @param $slug
     * @return DaycareSingleApiResource|JsonResponse
     */
    public function getDayCare($locale, $slug): DaycareSingleApiResource|JsonResponse
    {
        App::setLocale($locale);
        // load relations
        $daycare = Daycare::with(['images', 'features', 'ratings', 'categories'])
            ->whereTranslation('slug', $slug)
            ->first();

        if ($daycare) {
            return new DaycareSingleApiResource($daycare);
        } else {
            return response()->json([
                'data' => collect()
            ]);
        }
    }
}
