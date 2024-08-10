<?php

namespace App\Http\Controllers\Daycare;

use App\Models\Daycare\DaycareFeature;
use App\Models\Daycare\DaycareFeatureTranslation;
use App\Http\Controllers\Controller;
use App\Http\Resources\Daycare\DaycareFeatureResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Throwable;

class DaycareFeatureController extends Controller
{

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('viewAny daycare_feature');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $features = DaycareFeature::latest();
        if ($query) {
            $features = DaycareFeature::whereTranslationLike('title', '%' . $query . '%');
        }
        if ($sortBy) {
            $features = DaycareFeature::orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $features->get();
            $features = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $features = $features->paginate($per_page);
        }

        return DaycareFeatureResource::collection($features);
    }

    /**
     * Get all features
     *
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getAll($locale)
    {
        App::setLocale($locale);

        $this->authorize('viewAny daycare_feature');

        $features = DB::table('daycare_features as df')
            ->join('daycare_feature_translations as dft', 'df.id', '=', 'dft.daycare_feature_id')
            ->select('df.id', 'dft.title')
            ->where('dft.locale', '=', $locale)
            ->where('df.status', '=', 'active')
            ->get();

        return response()->json([
            'data' => $features
        ], 200);
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

        $this->authorize('create daycare_feature');

        // begin database transaction
        DB::beginTransaction();
        try {
            $features = DaycareFeature::create();

            // commit changes
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.create'),
                'featureId' => $features->id
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
     * Show feature.
     *
     * @param $locale
     * @param DaycareFeature $feature
     * @return DaycareFeatureResource|JsonResponse
     */
    public function show($locale, DaycareFeature $feature)
    {
        App::setLocale($locale);

        try {
            return new DaycareFeatureResource($feature);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * Edit feature.
     *
     * @param $locale
     * @param DaycareFeature $feature
     * @return DaycareFeatureResource|JsonResponse
     */
    public function edit($locale, DaycareFeature $feature)
    {
        App::setLocale($locale);

        try {
            return new DaycareFeatureResource($feature);
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
     * @param DaycareFeature $DaycareFeature
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $locale, DaycareFeature $feature)
    {
        App::setLocale($locale);

        $this->authorize('update daycare_feature');

        $this->validate($request, [
            'title' => 'required'
        ]);

        // begin database transaction
        DB::beginTransaction();
        try {
            $feature->update($request->all());

            // commit changes
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.update'),
                'all' => $request->all()
            ], 200);
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
     * @param DaycareFeature $feature
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($locale, DaycareFeature $feature)
    {
        App::setLocale($locale);

        $this->authorize('delete daycare_feature');

        // begin database transaction
        DB::beginTransaction();
        try {
            $feature->delete();

            // commit changes
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.delete')
            ], 200);
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
     * Get trashed features
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getTrashed(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('viewAny daycare_feature');

        $per_page = empty($request->query('per_page')) ? 10 : (int)$request->query('per_page');

        $features = DaycareFeature::onlyTrashed()->latest()->paginate($per_page);

        return response()->json([
            'data' => $features
        ], 200);
    }

    /**
     * Restore all trashed features
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function restoreTrashed(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('restore daycare_feature');

        $ids = explode(',', $request->get('ids'));

        // begin database transaction
        DB::beginTransaction();
        try {
            if (isset($ids)) {
                DaycareFeature::onlyTrashed()
                    ->whereIn('id', $ids)
                    ->restore();
            } else {
                DaycareFeature::onlyTrashed()->restore();
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
     * Restore single trashed feature
     *
     * @param $locale
     * @param $id
     * @return mixed
     * @throws AuthorizationException
     */
    public function restoreSingleTrashed($locale, $id)
    {
        App::setLocale($locale);

        $this->authorize('restore daycare_feature');

        // begin database transaction
        DB::beginTransaction();
        try {
            DaycareFeature::onlyTrashed()
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
     * Permanently delete all trashed features
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceDelete(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('forceDelete daycare_feature');

        $ids = explode(',', $request->get('ids'));

        // begin database transaction
        DB::beginTransaction();
        try {
            if (isset($ids)) {
                $features = DaycareFeature::onlyTrashed()
                    ->whereIn('id', $ids)
                    ->get();
            } else {
                $features = DaycareFeature::onlyTrashed()->get();
            }
            foreach ($features as $feature) {
                // delete related products
                $feature->products()->detach();
                // delete feature
                $feature->forceDelete();
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
     * Permanently delete single trashed feature
     *
     * @param $locale
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceSingleDelete($locale, $id)
    {
        App::setLocale($locale);

        $this->authorize('forceDelete daycare_feature');

        // begin database transaction
        DB::beginTransaction();
        try {
            $feature = DaycareFeature::onlyTrashed()
                ->where('id', '=', $id);

            // delete related products
            $feature->products()->detach();
            // delete feature
            $feature->forceDelete();

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
     * @param $locale
     * @param $title
     * @return string
     */
    public function checkSlug($locale, $title)
    {
        try {
            $slug = Str::slug($title, '-', $locale);
            # slug repeat check
            $latest = DaycareFeatureTranslation::where('slug', '=', $slug)
                ->latest('id')
                ->value('slug');

            if ($latest) {
                $pieces = explode('-', $latest);
                $number = intval(end($pieces));
                $slug .= '-' . ($number + 1);
            }
            return $slug;
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // return failed message
            return null;
        }
    }
}
