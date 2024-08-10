<?php

namespace App\Http\Controllers\Marketing;

use App\Models\Marketing\Service;
use App\Models\Marketing\ServiceTranslation;
use App\Http\Controllers\Controller;
use App\Http\Resources\Marketing\ServiceEditResource;
use App\Http\Resources\Marketing\ServiceResource;
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

class ServiceController extends Controller
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

        $this->authorize('viewAny service');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $services = Service::latest();
        if ($query) {
            $services = Service::whereTranslationLike('title', '%' . $query . '%');
        }
        if ($sortBy) {
            $services = Service::orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $services->get();
            $services = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $services = $services->paginate($per_page);
        }
        return ServiceResource::collection($services);
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

        $this->authorize('create service');

        // begin database transaction
        DB::beginTransaction();
        try {
            $service = Service::create([
                'user_id' => auth()->id(),
                'datetime' => now()
            ]);

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.create'),
                'serviceId' => $service->id
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
     * @param Service $service
     * @return ServiceEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show($locale, Service $service)
    {
        App::setLocale($locale);

        $this->authorize('view service');

        try {
            return new ServiceEditResource($service);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * Edit service.
     *
     * @param $locale
     * @param Service $service
     * @return ServiceEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function edit($locale, Service $service)
    {
        App::setLocale($locale);

        $this->authorize('update service');

        try {
            return new ServiceEditResource($service);
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
     * @param Service $service
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, $locale, Service $service)
    {
        
        App::setLocale($locale);

        $this->authorize('update service');

        // begin database transaction
        DB::beginTransaction();
        try {

            $service->update($request->all());
            
            // Update service categories
            if ($request->filled('categories')) {
                $items = collect($request->input('categories'))->pluck('id');
                $service->categories()->sync($items);
            }

            if ($request->filled('tags')) {
                $items = collect($request->input('tags'))->pluck('id');
                $service->tags()->sync($items);
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
     * @param Service $service
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($locale, Service $service)
    {
        App::setLocale($locale);

        $this->authorize('delete service');

        // begin database transaction
        DB::beginTransaction();
        try {
            // delete service
            $service->delete();

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
     * Get trashed services
     *
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function getTrashed(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('viewAny service');

        // $per_page = empty($request->query('per_page')) ? 10 : (int)$request->query('per_page');
        // $services = Service::onlyTrashed()->latest()->paginate($per_page);

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $services = Service::onlyTrashed();
        if ($query) {
            $services = $services->whereTranslationLike('title', '%' . $query . '%');
        }

        if ($sortBy) {
            $services = $services->orderBy($sortBy, $direction);
        } else {
            $services = $services->latest();
        }

        if ($per_page === '-1') {
            $results = $services->get();
            $services = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $services = $services->paginate($per_page);
        }

        return ServiceResource::collection($services);
    }

    /**
     * Restore all trashed services
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function restoreTrashed(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('restore service');

        $ids = explode(',', $request->get('ids'));

        // begin database transaction
        DB::beginTransaction();
        try {
            if (isset($ids)) {
                Service::onlyTrashed()
                    ->whereIn('id', $ids)
                    ->restore();
            } else {
                Service::onlyTrashed()->restore();
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
     * Restore single trashed service
     *
     * @param $locale
     * @param $id
     * @return mixed
     * @throws AuthorizationException
     */
    public function restoreSingleTrashed($locale, $id)
    {
        App::setLocale($locale);

        $this->authorize('restore service');

        // begin database transaction
        DB::beginTransaction();
        try {
            Service::onlyTrashed()
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
     * Permanently delete all trashed services
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceDelete(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('forceDelete service');

        $ids = explode(',', $request->get('ids'));

        // begin database transaction
        DB::beginTransaction();
        try {
            if (isset($ids)) {
                $services = Service::onlyTrashed()
                    ->whereIn('id', $ids)
                    ->get();
            } else {
                $services = Service::onlyTrashed()->get();
            }
            foreach ($services as $service) {
                // delete tag
                $service->tags()->detach();
                // delete service
                $service->forceDelete();
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
     * Permanently delete single trashed service
     *
     * @param $locale
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceSingleDelete($locale, $id)
    {
        App::setLocale($locale);

        $this->authorize('forceDelete service');

        // begin database transaction
        DB::beginTransaction();
        try {
            $service = Service::onlyTrashed()
                ->where('id', '=', $id)
                ->first();

            // delete tag
            $service->tags()->detach();
            // delete service
            $service->forceDelete();

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
     * Create slug for service.
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
            $latest = ServiceTranslation::where('slug', '=', $slug)
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
     * Permanently delete all trashed serviceOutlets
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getAll(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('viewAny service');

        // begin database transaction
        DB::beginTransaction();
        try {
           
            $services = Service::with('translations')->latest()
                ->where('status', '=', 1)
                ->get()
                ->map(function ($service) {
                    return [
                        'id'            => $service->id,
                        'name'         => $service->name,
                    ];
                });

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'data' => $services,
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
