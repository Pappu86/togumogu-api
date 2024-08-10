<?php

namespace App\Http\Controllers\Brand;

use App\Models\Brand\BrandOutlet;
use App\Models\Brand\BrandOutletTranslation;
use App\Http\Controllers\Controller;
use App\Http\Resources\Brand\BrandOutletEditResource;
use App\Http\Resources\Brand\BrandOutletResource;
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

class BrandOutletController extends Controller
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

        $this->authorize('viewAny brand_outlet');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $brandOutlets = BrandOutlet::latest();
        if ($query) {
            $brandOutlets = BrandOutlet::whereTranslationLike('title', '%' . $query . '%');
        }
        if ($sortBy) {
            $brandOutlets = BrandOutlet::orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $brandOutlets->get();
            $brandOutlets = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $brandOutlets = $brandOutlets->paginate($per_page);
        }
        return BrandOutletResource::collection($brandOutlets);
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

        $this->authorize('create brand_outlet');

        // begin database transaction
        DB::beginTransaction();
        try {
            $brandOutlet = BrandOutlet::create([
                'user_id' => auth()->id(),
                'datetime' => now()
            ]);

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.create'),
                'brandId' => $brandOutlet->id
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
     * @param BrandOutlet $brandOutlet
     * @return BrandOutletEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show($locale, BrandOutlet $brandOutlet)
    {
        App::setLocale($locale);

        $this->authorize('view brand_outlet');

        try {
            return new BrandOutletEditResource($brandOutlet);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * Edit Brand Outlet.
     *
     * @param $locale
     * @param BrandOutlet $brandOutlet
     * @return BrandOutletEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function edit($locale, BrandOutlet $brandOutlet)
    {
        App::setLocale($locale);

        $this->authorize('update brand_outlet');

        try {
            return new BrandOutletEditResource($brandOutlet);
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
     * @param BrandOutlet $brandOutlet
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, $locale, BrandOutlet $brandOutlet)
    {
        
        App::setLocale($locale);

        $this->authorize('update brand_outlet');

        // begin database transaction
        DB::beginTransaction();
        try {

            $brandOutlet->update($request->all());
           
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
     * @param BrandOutlet $brandOutlet
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($locale, BrandOutlet $brandOutlet)
    {
        App::setLocale($locale);

        $this->authorize('delete brand_outlet');

        // begin database transaction
        DB::beginTransaction();
        try {
            // delete brandOutlet
            $brandOutlet->delete();

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
     * Get trashed brandOutlets
     *
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function getTrashed(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('viewAny brand_outlet');

        // $per_page = empty($request->query('per_page')) ? 10 : (int)$request->query('per_page');
        // $brandOutlets = Brand::onlyTrashed()->latest()->paginate($per_page);

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $brandOutlets = BrandOutlet::onlyTrashed();
        if ($query) {
            $brandOutlets = $brandOutlets->whereTranslationLike('name', '%' . $query . '%');
        }

        if ($sortBy) {
            $brandOutlets = $brandOutlets->orderBy($sortBy, $direction);
        } else {
            $brandOutlets = $brandOutlets->latest();
        }

        if ($per_page === '-1') {
            $results = $brandOutlets->get();
            $brandOutlets = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $brandOutlets = $brandOutlets->paginate($per_page);
        }

        return BrandOutletResource::collection($brandOutlets);
    }

    /**
     * Restore all trashed brandOutlets
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function restoreTrashed(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('restore brand_outlet');

        $ids = explode(',', $request->get('ids'));

        // begin database transaction
        DB::beginTransaction();
        try {
            if (isset($ids)) {
                BrandOutlet::onlyTrashed()
                    ->whereIn('id', $ids)
                    ->restore();
            } else {
                BrandOutlet::onlyTrashed()->restore();
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
     * Restore single trashed brandOutlet
     *
     * @param $locale
     * @param $id
     * @return mixed
     * @throws AuthorizationException
     */
    public function restoreSingleTrashed($locale, $id)
    {
        App::setLocale($locale);

        $this->authorize('restore brand_outlet');

        // begin database transaction
        DB::beginTransaction();
        try {
            BrandOutlet::onlyTrashed()
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
     * Permanently delete all trashed brandOutlets
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceDelete(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('forceDelete brand_outlet');

        $ids = explode(',', $request->get('ids'));

        // begin database transaction
        DB::beginTransaction();
        try {
            if (isset($ids)) {
                $brandOutlets = BrandOutlet::onlyTrashed()
                    ->whereIn('id', $ids)
                    ->get();
            } else {
                $brandOutlets = BrandOutlet::onlyTrashed()->get();
            }
            foreach ($brandOutlets as $brandOutlet) {
                // delete tag
                $brandOutlet->tags()->detach();
                // delete brandOutlet
                $brandOutlet->forceDelete();
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
     * Permanently delete single trashed brandOutlet
     *
     * @param $locale
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceSingleDelete($locale, $id)
    {
        App::setLocale($locale);

        $this->authorize('forceDelete brand_outlet');

        // begin database transaction
        DB::beginTransaction();
        try {
            $brandOutlet = BrandOutlet::onlyTrashed()
                ->where('id', '=', $id)
                ->first();

            // delete tag
            $brandOutlet->tags()->detach();
            // delete brandOutlet
            $brandOutlet->forceDelete();

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
     * Create slug for brandOutlet.
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
            $latest = BrandOutletTranslation::where('slug', '=', $slug)
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
}
