<?php

namespace App\Http\Controllers\Menstrual;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Menstrual\MenstrualCalendar;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Carbon\Carbon;
use App\Traits\MenstrualHelpers;
use App\Http\Resources\Menstrual\MenstrualResource;
use Illuminate\Support\Facades\Log;


class MenstrualApiController extends Controller
{
    use MenstrualHelpers;
    /**
     * Create the specified resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'start_date' => 'required',
            // 'active_days' => 'required',
        ]);         

        // begin database transaction
        DB::beginTransaction();
        try {
            $customerId = Auth::id();
            $start_date = $request->get('start_date');
            $activeDays = $request->get('active_days');
            $cycleLength = $request->get('cycle_length');
            $menstrualData = $this->menstrualDateCalculation($start_date, $activeDays, $cycleLength);
            $menstrualData['customer_id'] = $customerId;
            $menstrual = MenstrualCalendar::create($menstrualData);
            if($menstrual && $menstrual->id){
               $menstrual= MenstrualCalendar::query()->where('id', $menstrual->id)->first();
            }
            // commit database
             DB::commit();
            return response()->json([
                'message' => Lang::get('crud.create'),
                'menstrualId' => $menstrual->id
            ]);
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // rollback database
            DB::rollBack();
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage(),
            ], 400);
        }
    }

    /**
     * @return MenstrualResource
     */
     public function getSingleMenstrualInfo(): MenstrualResource
     {
        try {
           $customerId = Auth::id();
            $menstrualInfo = MenstrualCalendar::query()->where('customer_id', '=', $customerId)->latest()->first();          
            $menstrualData=null;            
            if(!empty($menstrualInfo)){
                $menstrualData=new MenstrualAppResource($menstrualInfo);
            }
            return response()->json([
                    'data' => $menstrualData
                ],200); 
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }
}
