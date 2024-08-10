<?php

namespace App\Http\Controllers\Marketing;

use App\Models\Marketing\Offer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Marketing\OfferSingleAppResource;
use App\Http\Resources\Marketing\OfferAppResource;
use App\Models\Marketing\OfferRedeem;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Throwable;

class OfferAppController extends Controller
{
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
        $customerId = Auth::id();
        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction', 'asc');
        $per_page = $request->query('per_page', 10);

        $offers = Offer::latest()->where('status', '=', 1)
            ->where('end_date', '>', Carbon::now());

        //Filters query
        //My offers
        if ($sortBy === 'my_offer') {
            $offerIds = OfferRedeem::query()->where('expired_date', '>', Carbon::now())
                ->where('customer_id', $customerId)
                ->orderBy('expired_date', $direction)
                ->pluck('offer_id')->toArray();
            $ids_ordered = implode(',', $offerIds);
            $offers = Offer::where('status', '=', 1)
                ->whereIn('id', $offerIds)
                ->orderByRaw("FIELD(id, $ids_ordered)");

        } if ($sortBy === 'ending_soon') {
            $offerIds = OfferRedeem::query()->where('expired_date', '>', Carbon::now())
                ->where('customer_id', $customerId)
                ->orderBy('expired_date', $direction)
                ->pluck('offer_id')->toArray();

            // Soon ending
            $offersIdsList = Offer::where('end_date', '>', Carbon::now())
                ->where('status', '=', 1)
                ->pluck('id')->toArray();
            $ids = [...$offerIds, ...$offersIdsList];

            $offers = Offer::where('status', '=', 1)
                ->whereIn('id', $ids)
                ->orderBy('end_date', $direction);
        }

        if ($query) {
            $offers = $offers->whereTranslationLike('title', '%' . $query . '%')
                ->orWhereTranslationLike('short_description', '%' . $query . '%')
                ->orWhereTranslationLike('long_description', '%' . $query . '%');
        }

        if ($per_page === '-1') {
            $results = $offers->get();
            $offers = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $offers = $offers->paginate($per_page);
        }
        return OfferAppResource::collection($offers);
    }

    /**
     * @param $locale
     * @param $slug
     * @return OfferSingleAppResource|JsonResponse
     */
    public function getSingle($locale, $slug): OfferSingleAppResource|JsonResponse
    {
        App::setLocale($locale);
        // load relations
        $offer = Offer::with(['tags', 'categories'])
            ->whereTranslation('slug', $slug)
            ->first();

        if ($offer) {
            return new OfferSingleAppResource($offer);
        } else {
            return response()->json([
                'data' => collect()
            ]);
        }
    }

}
