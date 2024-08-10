<?php

namespace App\Http\Controllers\Marketing;

use App\Models\Marketing\Offer;
use App\Models\Marketing\OfferTranslation;
use App\Http\Controllers\Controller;
use App\Http\Resources\Marketing\OfferEditResource;
use App\Http\Resources\Marketing\OfferResource;
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

class OfferController extends Controller
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

        $this->authorize('viewAny offer');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $offers = Offer::latest();
        if ($query) {
            $offers = Offer::whereTranslationLike('title', '%' . $query . '%');
        }
        if ($sortBy) {
            $offers = Offer::orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $offers->get();
            $offers = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $offers = $offers->paginate($per_page);
        }
        return OfferResource::collection($offers);
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

        $this->authorize('create offer');

        // begin database transaction
        DB::beginTransaction();
        try {
            $offer = Offer::create([
                'user_id' => auth()->id(),
                'datetime' => now()
            ]);

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.create'),
                'offerId' => $offer->id
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
     * @param Offer $offer
     * @return OfferEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show($locale, Offer $offer)
    {
        App::setLocale($locale);

        $this->authorize('view offer');

        try {
            return new OfferEditResource($offer);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * Edit offer.
     *
     * @param $locale
     * @param Offer $offer
     * @return OfferEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function edit($locale, Offer $offer)
    {
        App::setLocale($locale);

        $this->authorize('update offer');

        try {
            return new OfferEditResource($offer);
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
     * @param Offer $offer
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, $locale, Offer $offer)
    {
        
        App::setLocale($locale);

        $this->authorize('update offer');

        // begin database transaction
        DB::beginTransaction();
        try {

            $offer->update($request->all());
            
            // Update offer categories
            if ($request->filled('categories')) {
                $items = collect($request->input('categories'))->pluck('id');
                $offer->categories()->sync($items);
            }

            if ($request->filled('tags')) {
                $items = collect($request->input('tags'))->pluck('id');
                $offer->tags()->sync($items);
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
     * @param Offer $offer
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($locale, Offer $offer)
    {
        App::setLocale($locale);

        $this->authorize('delete offer');

        // begin database transaction
        DB::beginTransaction();
        try {
            // delete offer
            $offer->delete();

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
     * Get trashed offers
     *
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function getTrashed(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('viewAny offer');

        // $per_page = empty($request->query('per_page')) ? 10 : (int)$request->query('per_page');
        // $offers = Offer::onlyTrashed()->latest()->paginate($per_page);

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $offers = Offer::onlyTrashed();
        if ($query) {
            $offers = $offers->whereTranslationLike('title', '%' . $query . '%');
        }

        if ($sortBy) {
            $offers = $offers->orderBy($sortBy, $direction);
        } else {
            $offers = $offers->latest();
        }

        if ($per_page === '-1') {
            $results = $offers->get();
            $offers = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $offers = $offers->paginate($per_page);
        }

        return OfferResource::collection($offers);
    }

    /**
     * Restore all trashed offers
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function restoreTrashed(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('restore offer');

        $ids = explode(',', $request->get('ids'));

        // begin database transaction
        DB::beginTransaction();
        try {
            if (isset($ids)) {
                Offer::onlyTrashed()
                    ->whereIn('id', $ids)
                    ->restore();
            } else {
                Offer::onlyTrashed()->restore();
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
     * Restore single trashed offer
     *
     * @param $locale
     * @param $id
     * @return mixed
     * @throws AuthorizationException
     */
    public function restoreSingleTrashed($locale, $id)
    {
        App::setLocale($locale);

        $this->authorize('restore offer');

        // begin database transaction
        DB::beginTransaction();
        try {
            Offer::onlyTrashed()
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
     * Permanently delete all trashed offers
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceDelete(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('forceDelete offer');

        $ids = explode(',', $request->get('ids'));

        // begin database transaction
        DB::beginTransaction();
        try {
            if (isset($ids)) {
                $offers = Offer::onlyTrashed()
                    ->whereIn('id', $ids)
                    ->get();
            } else {
                $offers = Offer::onlyTrashed()->get();
            }
            foreach ($offers as $offer) {
                // delete tag
                $offer->tags()->detach();
                // delete offer
                $offer->forceDelete();
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
     * Permanently delete single trashed offer
     *
     * @param $locale
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceSingleDelete($locale, $id)
    {
        App::setLocale($locale);

        $this->authorize('forceDelete offer');

        // begin database transaction
        DB::beginTransaction();
        try {
            $offer = Offer::onlyTrashed()
                ->where('id', '=', $id)
                ->first();

            // delete tag
            $offer->tags()->detach();
            // delete offer
            $offer->forceDelete();

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
     * Create slug for offer.
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
            $latest = OfferTranslation::where('slug', '=', $slug)
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
     * Permanently delete all trashed offerOutlets
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getAll(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('viewAny offer');

        // begin database transaction
        DB::beginTransaction();
        try {
           
            $offers = Offer::with('translations')->latest()
                ->where('status', '=', 1)
                ->get()
                ->map(function ($offer) {
                    return [
                        'id'            => $offer->id,
                        'name'         => $offer->name,
                    ];
                });

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'data' => $offers,
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
