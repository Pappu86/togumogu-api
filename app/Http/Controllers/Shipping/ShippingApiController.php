<?php

namespace App\Http\Controllers\Shipping;

use App\Http\Controllers\Controller;
use App\Models\User\Customer;
use App\Http\Resources\Corporate\EmployeeApiResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Throwable;

class ShippingApiController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getShippingCost(Request $request): JsonResponse
    {

        $area_id = $request->query('areaId');
        $customer_id = $request->query('customerId');

        $cost_info = DB::table('shipping_costs')
            ->where('area_id', '=', $area_id)
            ->first();

        // Set default shipping cost
        $total_order_amount = $request->query('total_amount');
        $cost = $cost_info->cost;
        $shipping_cart_amount = $cost_info->cart_amount?:0;

        // Public shipping cost
        if( $shipping_cart_amount && $total_order_amount &&  (float)$total_order_amount >= (float)$shipping_cart_amount ) {
            $cost = $cost_info->special_delivery_cost?:0;
        }

        if($request->filled('customerId')) {
            // Check normal user and corporate user
            $customer = Customer::where('id', '=', $customer_id)->first();
            
            // Check corporate user
            $corporate_user = $customer?->employee?new EmployeeApiResource($customer->employee):null;   
            $is_verified_corporate_customer = !!(isset($corporate_user) && $corporate_user->is_registered);
            
            // Corporate shipping cost
            if($is_verified_corporate_customer) {
                $partnership_info = $corporate_user?->partnership?:'';
               
                if($partnership_info?->is_free_shipping && ((float)$total_order_amount >= (float)$partnership_info->free_shipping_spend)) {
                    $cost = 0;
                } 
            }
        }

        return response()->json([
            'data' => $cost?:0
        ]);
    }

    /**
     * @param string $locale
     * @return JsonResponse
     */
    public function getDivisions($locale = 'en'): JsonResponse
    {
        try {
            $name = match ($locale) {
                'bn' => 'bn_name as name',
                default => 'name',
            };
            $divisions = DB::table('divisions')
                ->where('status', '=', 'active')
                ->select('id', $name)
                ->get();

            return response()->json($divisions);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json(collect());
        }
    }

    /**
     * @param $locale
     * @param $division
     * @return JsonResponse
     */
    public function getDistricts($locale, $division): JsonResponse
    {
        try {
            $name = match ($locale) {
                'bn' => 'bn_name as name',
                default => 'name',
            };
            $districts = DB::table('districts')
                ->where('division_id', '=', $division)
                ->where('status', '=', 'active')
                ->select('id', $name)
                ->get();

            return response()->json($districts);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json(collect());
        }
    }

    /**
     * @param $locale
     * @param $district
     * @return JsonResponse
     */
    public function getAreas($locale, $district): JsonResponse
    {
        try {
            $name = match ($locale) {
                'bn' => 'bn_name as name',
                default => 'name',
            };
            $areas = DB::table('areas')
                ->where('district_id', '=', $district)
                ->where('status', '=', 'active')
                ->select('id', $name)
                ->get();

            return response()->json($areas);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json(collect());
        }
    }
}
