<?php

namespace App\Http\Controllers\Marketing;

use App\Models\Marketing\Service;
use App\Http\Controllers\Controller;
use App\Http\Resources\Marketing\ServiceSingleAppResource;
use App\Http\Resources\Marketing\ServiceAppResource;
use App\Models\Marketing\ServiceRedeem;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Throwable;

class ServiceAppController extends Controller
{
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
        $customerId = Auth::id();
        $query = $request->query('query');
        $sortBy = $request->query('sortBy', 'created_at');
        $direction = $request->query('direction', 'desc');
        $per_page = $request->query('per_page', 10);
        $brandId = $request->query('brandId');

        $services = Service::with(['brand'])->where('status', '=', 1)
            ->where('end_date', '>', Carbon::now());
        
        if(isset($brandId)){
            $services = $services->where('brand_id', $brandId);
        };

        if ($query) {
            $services = $services->whereTranslationLike('title', '%' . $query . '%')
                ->orWhereTranslationLike('short_description', '%' . $query . '%')
                ->orWhereTranslationLike('long_description', '%' . $query . '%')
                ->orWhereHas('brand', function ($b) use ($query) {
                    $b->whereTranslationLike('name', '%' . $query . '%');
                });
        }
        
        if ($sortBy) {
            $services = $services->orderBy($sortBy, $direction);
        }

        if ($per_page === '-1') {
            $results = $services->get();
            $services = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $services = $services->paginate($per_page);
        }
        return ServiceAppResource::collection($services);
    }

    /**
     * @param $locale
     * @param $slug
     * @return ServiceSingleAppResource|JsonResponse
     */
    public function getSingle($locale, $slug): ServiceSingleAppResource|JsonResponse
    {
        App::setLocale($locale);
        // load relations
        $service = Service::with(['tags', 'categories'])
            ->whereTranslation('slug', $slug)
            ->first();

        if ($service) {
            $this->updateViewCount($service->id);    
            return new ServiceSingleAppResource($service);
        } else {
            return response()->json([
                'data' => collect()
            ]);
        }
    }

    /**
     * @param $service_id
     * @return JsonResponse
     */
    public function updateViewCount($service_id): JsonResponse
    {
        DB::beginTransaction();
        try {
            DB::table('services')
                ->where('id', '=', $service_id)
                ->increment('view_count');

            DB::commit();

            return response()->json([
                'message' => Lang::get('crud.update')
            ]);
        } catch (Throwable $exception) {
            report($exception);
            DB::rollBack();
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     */
    public function getTrackerService(Request $request, $locale)
    {
        App::setLocale($locale);
        $limit = (int)$request->query('limit', 1);
        $sort_by = $request->query('sort_by');
        $direction = $request->query('direction', 'asc');

        $serviceList = Service::with(['translations'])
            ->where('tracker', '=', $request->get('tracker_type'))
            ->where('tracker_start_day', '<=', $request->get('tracker_day'))
            ->where('tracker_end_day', '>=', $request->get('tracker_day'))
            ->where('status', '=', 1);

        if($serviceList->count() === 0 && !($request->get('tracker_type') ==='other')) {
            $serviceList = Service::with(['translations'])
                ->where('tracker_end_day', '>', $request->get('tracker_day'))
                ->where('status', '=', 1);
        };
        
        if ($sort_by) {
            $serviceList = $serviceList->orderBy($sort_by, $direction);
        }

        $serviceList = $serviceList->inRandomOrder()->paginate($limit);

        return ServiceAppResource::collection($serviceList);
    }

}
