<?php

namespace App\Http\Controllers\Blog;

use App\Jobs\DeepLink\AddArticleDeepLink;
use App\Models\Blog\Article;
use App\Models\Blog\Category;
use App\Models\Blog\ArticleTranslation;
use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\ArticleRequest;
use App\Http\Resources\Blog\ArticleEditResource;
use App\Http\Resources\Blog\ArticleResource;
use App\Jobs\DeepLink\AddArticleDeepLinkForFB;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Throwable;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('viewAny blog_article');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);
        $category_slug = $request->query('category');

        $articles = Article::latest();

        // Query by category
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

        if ($query) {
            $articles = Article::whereTranslationLike('title', '%' . $query . '%');
        }
        if ($sortBy) {
            $articles = Article::orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $articles->get();
            $articles = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $articles = $articles->paginate($per_page);
        }
        return ArticleResource::collection($articles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store($locale)
    {
        App::setLocale($locale);

        $this->authorize('create blog_article');

        // begin database transaction
        DB::beginTransaction();
        try {
            $article = Article::create([
                'user_id' => auth()->id(),
                'datetime' => now()
            ]);

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.create'),
                'articleId' => $article->id
            ], 201);
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

    /**
     * @param $locale
     * @param Article $article
     * @return ArticleEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show($locale, Article $article)
    {
        App::setLocale($locale);

        $this->authorize('view blog_article');

        try {
            return new ArticleEditResource($article);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * Edit article.
     *
     * @param $locale
     * @param Article $article
     * @return ArticleEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function edit($locale, Article $article)
    {
        App::setLocale($locale);

        $this->authorize('update blog_article');

        try {
            return new ArticleEditResource($article);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ArticleRequest $request
     * @param $locale
     * @param Article $article
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ArticleRequest $request, $locale, Article $article)
    {

        App::setLocale($locale);

        $this->authorize('update blog_article');

        // begin database transaction
        DB::beginTransaction();
        try {

            $article->update($request->all());

            // Update article categories
            if ($request->filled('categories')) {
                $items = collect($request->input('categories'))->pluck('id');
                $article->categories()->sync($items);
            }

            if ($request->filled('tags')) {
                $items = collect($request->input('tags'))->pluck('id');
                $article->tags()->sync($items);
            }

            // commit database
            DB::commit();

            // return success message
            return response()->json([
                'message' => Lang::get('crud.update')
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

    /**
     * Remove the specified resource from storage.
     *
     * @param $locale
     * @param Article $article
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($locale, Article $article)
    {
        App::setLocale($locale);

        $this->authorize('delete blog_article');

        // begin database transaction
        DB::beginTransaction();
        try {
            // delete article
            $article->delete();

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.trash')
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

    /**
     * Get trashed articles
     *
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function getTrashed(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('viewAny blog_article');

        // $per_page = empty($request->query('per_page')) ? 10 : (int)$request->query('per_page');
        // $articles = Article::onlyTrashed()->latest()->paginate($per_page);

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $articles = Article::onlyTrashed();
        if ($query) {
            $articles = $articles->whereTranslationLike('title', '%' . $query . '%');
        }

        if ($sortBy) {
            $articles = $articles->orderBy($sortBy, $direction);
        } else {
            $articles = $articles->latest();
        }

        if ($per_page === '-1') {
            $results = $articles->get();
            $articles = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $articles = $articles->paginate($per_page);
        }

        return ArticleResource::collection($articles);
    }

    /**
     * Restore all trashed articles
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function restoreTrashed(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('restore blog_article');

        $ids = explode(',', $request->get('ids'));

        // begin database transaction
        DB::beginTransaction();
        try {
            if (isset($ids)) {
                Article::onlyTrashed()
                    ->whereIn('id', $ids)
                    ->restore();
            } else {
                Article::onlyTrashed()->restore();
            }

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.restore')
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

    /**
     * Restore single trashed article
     *
     * @param $locale
     * @param $id
     * @return mixed
     * @throws AuthorizationException
     */
    public function restoreSingleTrashed($locale, $id)
    {
        App::setLocale($locale);

        $this->authorize('restore blog_article');

        // begin database transaction
        DB::beginTransaction();
        try {
            Article::onlyTrashed()
                ->where('id', '=', $id)
                ->restore();

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.restore')
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

    /**
     * Permanently delete all trashed articles
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceDelete(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('forceDelete blog_article');

        $ids = explode(',', $request->get('ids'));

        // begin database transaction
        DB::beginTransaction();
        try {
            if (isset($ids)) {
                $articles = Article::onlyTrashed()
                    ->whereIn('id', $ids)
                    ->get();
            } else {
                $articles = Article::onlyTrashed()->get();
            }
            foreach ($articles as $article) {
                // delete tag
                $article->tags()->detach();
                // delete article
                $article->forceDelete();
            }

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.delete')
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

    /**
     * Permanently delete single trashed article
     *
     * @param $locale
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceSingleDelete($locale, $id)
    {
        App::setLocale($locale);

        $this->authorize('forceDelete blog_article');

        // begin database transaction
        DB::beginTransaction();
        try {
            $article = Article::onlyTrashed()
                ->where('id', '=', $id)
                ->first();

            // delete tag
            $article->tags()->detach();
            // delete article
            $article->forceDelete();

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.delete')
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

    /**
     * Create slug for article.
     *
     * @param $locale
     * @param $title
     * @return JsonResponse
     */
    public function checkSlug($locale, $title)
    {
        try {
            $slug = Str::slug($title, '-', $locale);
            # slug repeat check
            $latest = ArticleTranslation::where('slug', '=', $slug)
                ->latest('id')
                ->value('slug');

            if ($latest) {
                $pieces = explode('-', $latest);
                $number = intval(end($pieces));
                $slug .= '-' . ($number + 1);
            }
            return response()->json([
                'slug' => $slug
            ], 200);
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * @return JsonResponse
     */
    public function generateDynamicLinks(): JsonResponse
    {
        Article::with(['translations'])
            ->where('status', '=', 1)
            ->where('datetime', '<=', now()->toDateTimeString())
            ->get()
            ->map(function ($article) {
                // generate dynamic link
                AddArticleDeepLink::dispatch($article);
                AddArticleDeepLinkForFB::dispatch($article);
            });

        return response()->json('Generated Successfully');
    }
    
}
