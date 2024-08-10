<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Resources\Reward\RewardPointResource;
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
use Throwable;

class RewardPointController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny reward_points');

        $per_page = $request->query('per_page', 10);
        $direction = $request->query('direction');
        $sortBy = $request->query('sortBy');
        $query = $request->query('query');

        $RewardSettings = RewardSetting::query();

        if (isset($query)) {
            $RewardSettings = $RewardSettings->whereLike(['category', 'award_points'], '%' . $query . '%');
        }

        if (isset($direction)) {
            $RewardSettings = $RewardSettings->orderBy($sortBy, $direction);
        } else {
            $RewardSettings = $RewardSettings->latest();
        }

        if ($per_page === '-1') {
            $results = $RewardSettings->get();
            $RewardSettings = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $RewardSettings = $RewardSettings->paginate($per_page);
        }

        return RewardPointResource::collection($RewardSettings);
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
        $this->authorize('update reward_points');
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
     * @param Request $request
     * @return JsonResponse
     */
    public function getSetting(Request $request): JsonResponse
    {
        
        DB::beginTransaction();
        try {
           
            $category = $request->get('category');

            if(!isset($category)) {
                return response()->json([
                    'message' => 'Please, select a category!'
                ], 401);
            }

            $RewardSettings = RewardSetting::where('status', 'active')
                ->where('category', '=', $category)
                ->first();

            DB::commit();

            return response()->json($RewardSettings);

        } catch (Throwable $exception) {
            report($exception);
            DB::rollBack();

            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function getAllSettings(Request $request): AnonymousResourceCollection
    {

        $per_page = $request->query('per_page', 10);
        $direction = $request->query('direction');
        $sortBy = $request->query('sortBy');
        $query = $request->query('query');

        $RewardSettings = RewardSetting::query();

        if (isset($query)) {
            $RewardSettings = $RewardSettings->whereLike(['category', 'award_points'], '%' . $query . '%');
        }

        if (isset($direction)) {
            $RewardSettings = $RewardSettings->orderBy($sortBy, $direction);
        } else {
            $RewardSettings = $RewardSettings->latest();
        }

        if ($per_page === '-1') {
            $results = $RewardSettings->get();
            $RewardSettings = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $RewardSettings = $RewardSettings->paginate($per_page);
        }

        return RewardPointResource::collection($RewardSettings);
    }
}
