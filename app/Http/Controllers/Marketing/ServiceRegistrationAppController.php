<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\Service;
use App\Models\Marketing\ServiceRegistration;
use App\Models\Reward\RewardSetting;
use App\Models\Reward\RewardTransaction;
use App\Notifications\Reward\ServiceRegistrationRewardPoint;
// use App\Traits\CommonHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class ServiceRegistrationAppController extends Controller
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
            'service_id' => 'required',
            'brand_id' => 'required',
        ]);
        $serviceId = $request->get('service_id');
        $service = Service::where('id', $serviceId)->first();
       
        if(!!!$service) {
            return response()->json(['message' => Lang::get('validation.expired')], 422);
        };

        DB::beginTransaction();
        try {

            $customerInfo = $request->get('customer_info');
            $paymentStatus = $request->get('payment_status');
            
            $request->merge([
                'status' => 1,
                'service_reg_status' => 'pending',
                'payment_status' => $paymentStatus?:'later',
                'customer_id' => $customer->id,
                'current_location_name' => $customer?->current_location_name?:'',
                'current_longitude' => $customer?->current_longitude?:'',
                'current_latitude' => $customer?->current_latitude?:'',
                'price' => $service->price,
                'special_price' => $service->special_price,
                'service_reg_no' => $this->generateServiceRegistrationNumber(),
                'customer_info' => json_encode($customerInfo),
                'booking_info' => json_encode($request->get('booking_info')),
                'questions' => json_encode($request->get('questions'))
            ]);

            // Insert service registration
            $isCreated = ServiceRegistration::create($request->all());

            DB::commit();

            // Update service registration count
            if($isCreated) {
                $serviceCount = $service->registration_count?:0;
                $serviceCount = $serviceCount + 1;
                $service->update(['registration_count' => $serviceCount]);
            }

            // Update customer info
            $customerData = [];

            if(!($customer->email) && $customerInfo['email']) {
                $customerData['email'] = $customerInfo['email'];
            };

            if(!($customer->mobile) && $customerInfo['phone']) {
                $customerData['mobile'] = $customerInfo['phone'];
            };

            if(!($customer->name) && $customerInfo['name']) {
                $customerData['name'] = $customerInfo['name'];
            } else if($customer->name && $customerInfo['name']
                && ($customer?->name === $customer?->email || $customer?->name === $customer?->mobile)){
                    $customerData['name'] = $customerInfo['name'];
            };

            $customer->update($customerData);
            
            // // Added Reward points for profile updating
            // $commonHelpers = new CommonHelpers;
            // $progressPoint = $commonHelpers->getProfileProgress($customer);

            // if($progressPoint && $progressPoint >= 80) {
            //    $existReward = RewardTransaction::where('customer_id', $customer->id)
            //         ->where('category', 'customer')
            //         ->where('action', 'profile_updated')
            //         ->where('reference_id', $customer->id)
            //         ->first();

            //     if(!isset($existReward)) {
            //         $commonHelpers->addProfileUpdateRewardPoints($customer);
            //     }
            // }

            //Added reward point after service registration
            $rewardPoints = 0;
            if($customer?->id && $service?->id){
                $rewardPoints = $this->addServiceRegistrationRewardPoints($customer, $service);
            }

            return response()->json([
                'message' => Lang::get('crud.create'),
                'rewardPoints' => $rewardPoints
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
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param ServiceRegistration $serviceRegistration
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, ServiceRegistration $serviceRegistration)
    {
        $paymentMethod = $request->get('payment_method');

      
        if(!!!$paymentMethod) {
            return response()->json(['message' => Lang::get('Please select payment method!')], 422);
        };

        // begin database transaction
        DB::beginTransaction();
        try {

            $serviceRegistration->update([
                'payment_method' => $paymentMethod
            ]);
            
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
     * @return string
     */
    private function generateServiceRegistrationNumber(): string
    {
        $today = date("ym");
        $lastOfferRedeem = ServiceRegistration::latest()->first();
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


     /**
     * @param $customer
     * @param Service $service
     * @return int
     */
    public function addServiceRegistrationRewardPoints( $customer, Service $service): int
    {
        $rewardSetting = RewardSetting::where('category', 'service_registration')
                            ->where('status', 'active')
                            ->first();

        $points = $rewardSetting?->award_points?:0;
        $isReturn = false;

        if($points){
           $reward = RewardTransaction::create([
                'status' => 'active',
                'customer_id' => $customer?->id,
                'reference_id' => $service?->id,
                'credit' => $points,
                'reward_setting_id' => $rewardSetting->id,
                'category' => 'service',
                'debit' => 0,
                'description' => 'Service Registration',
                'action' => 'service_registration',
            ]);

            //Send database notifications 
            $customer?->notify((new ServiceRegistrationRewardPoint($customer, $service, $reward))); 

            $isReturn = true;
        }

        return $isReturn? $points:0;
    }

}
