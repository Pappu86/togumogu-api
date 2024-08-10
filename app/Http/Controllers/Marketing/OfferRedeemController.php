<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Resources\Marketing\OfferRedeemResource;
use App\Models\Marketing\OfferRedeem;
use App\Models\Reward\RewardSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;
use Throwable;

class OfferRedeemController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny offer_redeem');

        $per_page = $request->query('per_page', 10);
        $direction = $request->query('direction');
        $sortBy = $request->query('sortBy');
        $query = $request->query('query');

        $offerRedeems = OfferRedeem::query();

        if (isset($query)) {
            $offerRedeems = $offerRedeems->whereLike(['spent_reward_point'], '%' . $query . '%');
        }

        if (isset($direction)) {
            $offerRedeems = $offerRedeems->orderBy($sortBy, $direction);
        } else {
            $offerRedeems = $offerRedeems->latest();
        }

        if ($per_page === '-1') {
            $results = $offerRedeems->get();
            $offerRedeems = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $offerRedeems = $offerRedeems->paginate($per_page);
        }

        return OfferRedeemResource::collection($offerRedeems);
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

}
