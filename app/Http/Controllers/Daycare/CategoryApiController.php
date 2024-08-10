<?php

namespace App\Http\Controllers\Daycare;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryTreeResource;
use App\Http\Resources\Daycare\CategorySingleApiResource;
use App\Models\Daycare\DaycareCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Throwable;

class CategoryApiController extends Controller
{
    /**
     * @param $locale
     * @return AnonymousResourceCollection
     */
    public function getAll($locale): AnonymousResourceCollection
    {
        App::setLocale($locale);
        $categories = DaycareCategory::with('translations')
                ->where('status', '=', 'active')
                ->get();

        return CategoryTreeResource::collection($categories);
    }

    /**
     * @param $locale
     * @param $slug
     * @return CategorySingleApiResource
     */
    public function getSingleCategory($locale, $slug): CategorySingleApiResource
    {
        App::setLocale($locale);

        $category = DaycareCategory::with('translations')
            ->whereTranslation('slug', $slug)
            ->firstOrFail();

        // get category ids
        $daycares = DB::table('daycare_category_daycare')
            ->where('daycare_category_id', $category->id)
            ->count('daycare_id');
        if ($category) {
            $category->daycares_count = $daycares;
        }

        return new CategorySingleApiResource($category);
    }
}
