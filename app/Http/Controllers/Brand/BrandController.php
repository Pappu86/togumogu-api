<?php

namespace App\Http\Controllers\Brand;

use App\Models\Brand\Brand;
use App\Models\Brand\BrandTranslation;
use App\Http\Controllers\Controller;
use App\Http\Resources\Brand\BrandEditResource;
use App\Http\Resources\Brand\BrandResource;
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

class BrandController extends Controller
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

        $this->authorize('viewAny brand');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $brands = Brand::latest();
        if ($query) {
            $brands = Brand::whereTranslationLike('name', '%' . $query . '%');
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
        return BrandResource::collection($brands);
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

        $this->authorize('create brand');

        // begin database transaction
        DB::beginTransaction();
        try {
            $brand = Brand::create([
                'user_id' => auth()->id(),
                'datetime' => now()
            ]);

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.create'),
                'brandId' => $brand->id
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
     * @param Brand $brand
     * @return BrandEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show($locale, Brand $brand)
    {
        App::setLocale($locale);

        $this->authorize('view brand');

        try {
            return new BrandEditResource($brand);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * Edit brand.
     *
     * @param $locale
     * @param Brand $brand
     * @return BrandEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function edit($locale, Brand $brand)
    {
        App::setLocale($locale);

        $this->authorize('update brand');

        try {
            return new BrandEditResource($brand);
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
     * @param Brand $brand
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, $locale, Brand $brand)
    {
        
        App::setLocale($locale);

        $this->authorize('update brand');

        // begin database transaction
        DB::beginTransaction();
        try {

            $brand->update($request->all());
            
            // Update brand categories
            if ($request->filled('categories')) {
                $items = collect($request->input('categories'))->pluck('id');
                $brand->categories()->sync($items);
            }

            if ($request->filled('tags')) {
                $items = collect($request->input('tags'))->pluck('id');
                $brand->tags()->sync($items);
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
     * @param Brand $brand
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($locale, Brand $brand)
    {
        App::setLocale($locale);

        $this->authorize('delete brand');

        // begin database transaction
        DB::beginTransaction();
        try {
            // delete brand
            $brand->delete();

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
     * Get trashed brands
     *
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function getTrashed(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('viewAny brand');

        // $per_page = empty($request->query('per_page')) ? 10 : (int)$request->query('per_page');
        // $brands = Brand::onlyTrashed()->latest()->paginate($per_page);

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $brands = Brand::onlyTrashed();
        if ($query) {
            $brands = $brands->whereTranslationLike('title', '%' . $query . '%');
        }

        if ($sortBy) {
            $brands = $brands->orderBy($sortBy, $direction);
        } else {
            $brands = $brands->latest();
        }

        if ($per_page === '-1') {
            $results = $brands->get();
            $brands = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $brands = $brands->paginate($per_page);
        }

        return BrandResource::collection($brands);
    }

    /**
     * Restore all trashed brands
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function restoreTrashed(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('restore brand');

        $ids = explode(',', $request->get('ids'));

        // begin database transaction
        DB::beginTransaction();
        try {
            if (isset($ids)) {
                Brand::onlyTrashed()
                    ->whereIn('id', $ids)
                    ->restore();
            } else {
                Brand::onlyTrashed()->restore();
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
     * Restore single trashed brand
     *
     * @param $locale
     * @param $id
     * @return mixed
     * @throws AuthorizationException
     */
    public function restoreSingleTrashed($locale, $id)
    {
        App::setLocale($locale);

        $this->authorize('restore brand');

        // begin database transaction
        DB::beginTransaction();
        try {
            Brand::onlyTrashed()
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
     * Permanently delete all trashed brands
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceDelete(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('forceDelete brand');

        $ids = explode(',', $request->get('ids'));

        // begin database transaction
        DB::beginTransaction();
        try {
            if (isset($ids)) {
                $brands = Brand::onlyTrashed()
                    ->whereIn('id', $ids)
                    ->get();
            } else {
                $brands = Brand::onlyTrashed()->get();
            }
            foreach ($brands as $brand) {
                // delete tag
                $brand->tags()->detach();
                // delete brand
                $brand->forceDelete();
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
     * Permanently delete single trashed brand
     *
     * @param $locale
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceSingleDelete($locale, $id)
    {
        App::setLocale($locale);

        $this->authorize('forceDelete brand');

        // begin database transaction
        DB::beginTransaction();
        try {
            $brand = Brand::onlyTrashed()
                ->where('id', '=', $id)
                ->first();

            // delete tag
            $brand->tags()->detach();
            // delete brand
            $brand->forceDelete();

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
     * Create slug for brand.
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
            $latest = BrandTranslation::where('slug', '=', $slug)
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
     * Permanently delete all trashed brandOutlets
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getAll(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('viewAny brand');

        // begin database transaction
        DB::beginTransaction();
        try {
           
            $brands = Brand::with('translations')->latest()
                ->where('status', '=', 1)
                ->get()
                ->map(function ($brand) {
                    return [
                        'id'            => $brand->id,
                        'name'         => $brand->name,
                    ];
                });

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'data' => $brands,
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

}
