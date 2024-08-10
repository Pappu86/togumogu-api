<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;
use Throwable;
use App\Models\Corporate\CompanyCategory;
use App\Models\Corporate\CompanyCategoryTranslation;
use App\Http\Resources\Corporate\CompanyCategoryEditResource;
use App\Http\Resources\Corporate\CompanyCategoryResource;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Corporate\Company;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;


class CompanyCategoryController extends Controller
{

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request, $locale): AnonymousResourceCollection
    {
        App::setLocale($locale);

        $this->authorize('viewAny company_category');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $categories = CompanyCategory::latest();
        if ($query) {
            $categories = $categories->whereTranslationLike('name', '%' . $query . '%');
        }
        if ($sortBy) {
            $categories = CompanyCategory::orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $categories->get();
            $categories = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $categories = $categories->paginate($per_page);
        }

        return CompanyCategoryResource::collection($categories);

    }

    /**
     * Get all categories
     *
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getAll($locale): JsonResponse
    {
        $this->authorize('viewAny company_category');

        $categories = DB::table('company_categories as c')
            ->join('company_category_translations as ct', 'c.id', '=', 'ct.company_category_id')
            ->select('c.id', 'ct.name')
            ->where('ct.locale', '=', $locale)
            ->where('c.status', '=', 'active')
            ->get();

        return response()->json([
            'data' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store($locale): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('create company_category');

        // begin database transaction
        DB::beginTransaction();
        try {
            $category = CompanyCategory::create();

            // commit changes
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.create'),
                'categoryId' => $category->id
            ], 201);
        } catch (Throwable $exception) {
            report($exception);
            // rollback changes
            DB::rollBack();
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * Show category.
     *
     * @param $locale
     * @param CompanyCategory $companyCategory
     * @return JsonResponse|CompanyCategoryEditResource
     */
    public function show($locale, CompanyCategory $companyCategory): JsonResponse|CompanyCategoryEditResource
    {
        App::setLocale($locale);

        try {
            return new CompanyCategoryEditResource($companyCategory);
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
     * @param Request $request
     * @param $locale
     * @param CompanyCategory $companyCategory
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, $locale, CompanyCategory $companyCategory): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('update company_category');

        $this->validate($request, [
            'name' => 'required'
        ]);

        // begin database transaction
        DB::beginTransaction();
        try {
            $companyCategory->update($request->all());

            // commit changes
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.update')
            ]);
        } catch (Throwable $exception) {
            report($exception);
            // rollback changes
            DB::rollBack();
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $locale
     * @param CompanyCategory $companyCategory
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($locale, CompanyCategory $companyCategory): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('delete company_category');

        // begin database transaction
        DB::beginTransaction();
        try {
            $companyCategory->delete();

            // commit changes
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.delete')
            ]);
        } catch (Throwable $exception) {
            report($exception);
            // rollback changes
            DB::rollBack();
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * Get trashed categories
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getTrashed(Request $request, $locale): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('viewAny company_category');

        $per_page = empty($request->query('per_page')) ? 10 : (int)$request->query('per_page');

        $categories = CompanyCategory::onlyTrashed()->latest()->paginate($per_page);

        return response()->json([
            'data' => $categories
        ]);
    }

    /**
     * Restore all trashed categories
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function restoreTrashed(Request $request, $locale): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('restore company_category');

        $ids = explode(',', $request->get('ids'));

        // begin database transaction
        DB::beginTransaction();
        try {
            if (isset($ids)) {
                CompanyCategory::onlyTrashed()
                    ->whereIn('id', $ids)
                    ->restore();
            } else {
                CompanyCategory::onlyTrashed()->restore();
            }

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.restore')
            ]);
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
     * Restore single trashed category
     *
     * @param $locale
     * @param $id
     * @return mixed
     * @throws AuthorizationException
     */
    public function restoreSingleTrashed($locale, $id): mixed
    {
        App::setLocale($locale);

        $this->authorize('restore company_category');

        // begin database transaction
        DB::beginTransaction();
        try {
            CompanyCategory::onlyTrashed()
                ->where('id', '=', $id)
                ->restore();

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.restore')
            ]);
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
     * Permanently delete all trashed categories
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceDelete(Request $request, $locale): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('forceDelete company_category');

        $ids = explode(',', $request->get('ids'));

        // begin database transaction
        DB::beginTransaction();
        try {
            if (isset($ids)) {
                $categories = CompanyCategory::onlyTrashed()
                    ->whereIn('id', $ids)
                    ->get();
            } else {
                $categories = CompanyCategory::onlyTrashed()->get();
            }
            foreach ($categories as $category) {
                // delete related articles
                $category->articles()->delete();
                // delete category
                $category->forceDelete();
            }

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.delete')
            ]);
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
     * Permanently delete single trashed category
     *
     * @param $locale
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceSingleDelete($locale, $id): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('forceDelete company_category');

        // begin database transaction
        DB::beginTransaction();
        try {
            $category = CompanyCategory::onlyTrashed()
                ->where('id', '=', $id);

            // delete category
            $category->forceDelete();

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.delete')
            ]);
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
     * Rebuild category parent children.
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function rebuildTree(Request $request, $locale): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('update company_category');

        // begin database transaction
        DB::beginTransaction();
        try {
            // rearrange category
            CompanyCategory::rebuildTree($request->all());

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.update')
            ]);
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
    public function checkSlug($locale, $title): JsonResponse
    {
        try {
            $slug = Str::slug($title, '-', $locale);
            # slug repeat check
            $latest = CompanyCategoryTranslation::where('slug', '=', $slug)
                ->latest('id')
                ->value('slug');

            if ($latest) {
                $pieces = explode('-', $latest);
                $number = intval(end($pieces));
                $slug .= '-' . ($number + 1);
            }
            return response()->json([
                'slug' => $slug
            ]);
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }
}
