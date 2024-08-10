<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryTreeResource;
use App\Http\Resources\Blog\CategorySingleApiResource;
use App\Models\Blog\Category;
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
        $categories = Category::with('children')
            ->where('status', '=', 'active')
            ->whereIsRoot()->defaultOrder()->get();

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

        $category = Category::with('translations')
            ->whereTranslation('slug', $slug)
            ->firstOrFail();

        $ancestors = Category::with('translations')->ancestorsAndSelf($category)->pluck('id');
        $descendants = Category::with('translations')->descendantsAndSelf($category)->pluck('id');

        // get category ids
        $category_ids = collect($ancestors)->merge($descendants)->unique()->values()->toArray();
        $articles = DB::table('blog_article_category_article')
            ->whereIn('category_id', $category_ids)
            ->count('article_id');
        if ($category) {
            $category->articles_count = $articles;
        }

        return new CategorySingleApiResource($category);
    }


}
