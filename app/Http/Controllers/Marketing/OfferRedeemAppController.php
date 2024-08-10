<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\Offer;
use App\Models\Marketing\OfferRedeem;
use App\Models\Reward\RewardTransaction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;
use Throwable;

class OfferRedeemAppController extends Controller
{
   
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function store(Request $request): JsonResponse
    {
        $customer = Auth::user();
        $this->validate($request, [
            'offer_id' => 'required',
            'brand_id' => 'required',
            'reward_amount' => 'required',
            'validity_day' => 'required',
        ]);

        $redeemPoints = $request->get('reward_amount');
        $offerId = $request->get('offer_id');
        
        //Checking the offer has been expired
        $offer = Offer::where('id', $offerId)->first();
        $isOfferExpired = $offer? Carbon::now()->isAfter($offer?->end_date):false;

        if($isOfferExpired) {
            return response()->json(['message' => Lang::get('validation.expired')], 422);
        };

        if(Carbon::now()->isBefore($offer->start_date)) {
            return response()->json(['message' => Lang::get('validation.not_started_yet')], 422);
        };

        //Checking customer has already given the offer
        $lastOfferRedeem = OfferRedeem::latest()->where('customer_id', $customer->id)->where('offer_id', $offerId)->first();
        $isOfferContinue = $lastOfferRedeem? Carbon::now()->isBefore($lastOfferRedeem?->expired_date):false;
        if($isOfferContinue) {
            return response()->json(['message' => Lang::get('customer.already_taken')], 422);
        };

        //Checking customer reward balance
        $balance = $customer?->reward?->balance?:0;
        if(isset($balance) && $redeemPoints > $balance) {
            return response()->json(['message' => Lang::get('customer.insufficient_funds')], 422);
        };

        DB::beginTransaction();
        try {

            $validityDays = $request->get('validity_day');
            $request->merge([
                'status' => 1,
                'customer_id' => $customer->id,
                'coupon' => $offer->coupon,
                'spent_reward_point' => $redeemPoints,
                'start_date' => Carbon::now(),
                'expired_date' => Carbon::now()->addDays($validityDays),
                'offer_redeem_no' => $this->generateOfferRedeemNumber(),
            ]);

            // Insert offer redeem
            $offerRedeem = OfferRedeem::create($request->all());
            
            // Insert reward transaction
            if(isset($offerRedeem)) {
                RewardTransaction::create([
                    'action' => 'offer_redeem',
                    'category' => 'offer',
                    'customer_id' => $customer->id,
                    'debit' => $redeemPoints,
                    'reference_id' => $offerRedeem->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => Lang::get('crud.create')
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
     * @return string
     */
    private function generateOfferRedeemNumber(): string
    {
        $today = date("ym");
        $lastOfferRedeem = OfferRedeem::latest()->first();
        $lastOfferRedeemId = $lastOfferRedeem?->id?:0;
        $offerRedeemCount = $lastOfferRedeemId+1;
        $prefix = '';
        $string_length = strlen("$offerRedeemCount");
        if($string_length=='1') $prefix = '00000';
        else if($string_length=='2') $prefix = '0000';
        else if($string_length=='3') $prefix = '000';
        else if($string_length=='4') $prefix = '00';
        else if($string_length=='5') $prefix = '0';

        $order_serial_id = "{$prefix}{$offerRedeemCount}";
        return "{$today}{$order_serial_id}";
    }

}
