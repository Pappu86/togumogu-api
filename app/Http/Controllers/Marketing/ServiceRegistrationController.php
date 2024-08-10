<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Resources\Marketing\ServiceRegistrationResource;
use App\Http\Resources\Marketing\ServiceRegistrationSingleResource;
use App\Models\Marketing\ServiceRegistration;
use App\Models\Reward\RewardSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class ServiceRegistrationController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny service_registration');

        $per_page = $request->query('per_page', 10);
        $direction = $request->query('direction');
        $sortBy = $request->query('sortBy');
        $query = $request->query('query');
        $status = $request->query('status');
        $from_date = $request->query('fromDate');
        $to_date = $request->query('toDate');
        $orderStatus = $request->query('orderStatus');
        $brandIds = $request->query('brands');
        $serviceIds = $request->query('services');

        $serviceRegistration = ServiceRegistration::with(['service']);

        if (isset($query)) {
            $serviceRegistration = $serviceRegistration->whereLike(['service_reg_no'], '%' . $query . '%')
                 ->orWhereHas('service', function ($s) use ($query) {
                    $s->whereTranslationLike('title', '%' . $query . '%');
                });
        }


        $statusList = explode(",",$status);
        if (isset($status) && count($statusList) > 0) {
            $serviceRegistration = $serviceRegistration->whereIn('status', $statusList);
        };
        
        $orderStatusList = $orderStatus?explode(",",$orderStatus):[];
        if (isset($orderStatusList) && count($orderStatusList) > 0) {
            $serviceRegistration = $serviceRegistration->whereIn('service_reg_status', $orderStatusList);
        };
        
        $brandIdsList = $brandIds? explode(",",$brandIds):[];
        if (isset($brandIdsList) && count($brandIdsList) > 0) {
            $serviceRegistration = $serviceRegistration->whereIn('brand_id', $brandIdsList);
        };

        $serviceIdsList = $serviceIds? explode(",",$serviceIds):[];
        if (isset($serviceIdsList) && count($serviceIdsList) > 0) {
            $serviceRegistration = $serviceRegistration->whereIn('service_id', $serviceIdsList);
        };
        
        if (isset($to_date) && isset($from_date)) {
            $serviceRegistration = $serviceRegistration->whereBetween('created_at', [strval($from_date), strval($to_date)]);
        };

        if (isset($direction)) {
            $serviceRegistration = $serviceRegistration->orderBy($sortBy, $direction);
        } else {
            $serviceRegistration = $serviceRegistration->latest();
        }

        if ($per_page === '-1') {
            $results = $serviceRegistration->get();
            $serviceRegistration = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $serviceRegistration = $serviceRegistration->paginate($per_page);
        }

        return ServiceRegistrationResource::collection($serviceRegistration);
    }

    /**
     * @param Request $request
     * @param RewardSetting $rewardSetting
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function update(Request $request, RewardSetting $rewardSetting): JsonResponse
    {
        $this->authorize('update offer_redeem');
        $this->validate($request, [
            'category' => 'required',
            'award_points' => 'required',
        ]);
        DB::beginTransaction();
        try {
        
            $rewardSetting->update($request->all());

            DB::commit();

            return response()->json([
                'message' => Lang::get('crud.update')
            ]);

        } catch (Throwable $exception) {
            report($exception);
            DB::rollBack();

            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * @param $locale
     * @param Service $service
     * @return ServiceRegistrationSingleResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show(ServiceRegistration $serviceRegistration)
    {

        $this->authorize('view service_registration');

        try {
            return new ServiceRegistrationSingleResource($serviceRegistration);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * @param Request $request
     * @param ServiceRegistration $serviceRegistration
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function updateProcess(Request $request, ServiceRegistration $serviceRegistration): JsonResponse
    {
        $this->authorize('update service_registration');

        $request->validate([
            'service_reg_status' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $request->merge(['user_id' => auth()->id()]);
            $service_reg_status = $request->get('service_reg_status');
            $payment_status = $request->get('payment_status');
            $updatedOrderStatus = false;
            // check if these are changed
            if ($service_reg_status !== $serviceRegistration->service_reg_status) {
                $updatedOrderStatus = true;
            }

            $serviceRegistration->update([
                    'service_reg_status' => $service_reg_status,
                    'payment_status' => $payment_status,
                ]);

            // insert into order processes table
            $serviceRegistration->processes()->create($request->all());

            DB::commit();

            return response()->json([
                'message' => Lang::get('crud.update'),
                'updatedOrderStatus' => $updatedOrderStatus,
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


}
