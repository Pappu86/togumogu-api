<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Jobs\SendWelcomeMessage;
use App\Models\Message\Template;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class BookFairApiController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    function store(Request $request): JsonResponse
    {   
        $this->validate($request, [
            'name' => 'required',
            'mobile' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $customer = DB::table('bookfair_customers')
                ->where('mobile', $request->get('mobile'));

            if($customer->count()>0) {
                $customer = $customer->first();
            } else {
                DB::table('bookfair_customers')->insert([
                    'name' => $request->input('name'),
                    'mobile' => $request->input('mobile'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                
                $customer = DB::table('bookfair_customers')
                    ->where('mobile', $request->get('mobile'))
                    ->first();
            }

            DB::commit();

            $template = Template::with('translations')
                ->where('category', 'book_fair')
                ->where('status', 'active')
                ->where('type', 'sms')
                ->first();

            // try to send sms
            SendWelcomeMessage::dispatch($customer, $template?->content);
            
            return response()->json([
                'message' => 'Success!'
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();

            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
        }


    }
}
