<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\Corporate\Partnership;
use App\Http\Resources\Corporate\PartnershipResource;
use App\Http\Resources\Corporate\PartnershipEditResource;
use App\Jobs\DeepLink\AddReferralDeepLink;
use App\Models\Reward\Referral;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Throwable;

class PartnershipController extends Controller
{  
    /**
    * @param Request $request
    * @param $locale
    * @return AnonymousResourceCollection
    * @throws AuthorizationException
    */
   public function index(Request $request, $locale): AnonymousResourceCollection
   {
       App::setLocale($locale);

       $this->authorize('viewAny partnership');

       $query = $request->query('query');
       $sortBy = $request->query('sortBy');
       $group_id = $request->query('group_id');
       $direction = $request->query('direction');
       $per_page = $request->query('per_page', 10);

       $partnerships = Partnership::query()->latest();
       if ($query) {
           $partnerships = $partnerships->whereLike('name', '%' . $query . '%');
       }

       if ($group_id) {
            $partnerships = $partnerships->where('group_id', '=', $group_id);
        }

       if ($sortBy) {
           $partnerships = $partnerships->orderBy($sortBy, $direction);
       }

       if ($per_page === '-1') {
           $results = $partnerships->get();
           $partnerships = new LengthAwarePaginator($results, $results->count(), -1);
       } else {
           $partnerships = $partnerships->paginate($per_page);
       }
       
       return PartnershipResource::collection($partnerships);
   }

   /**
    * Store a newly created resource in storage.
    *
    * @param $request
    * @param $locale
    * @return JsonResponse
    * @throws AuthorizationException
    */
   public function store(Request $request, $locale): JsonResponse
   {
       App::setLocale($locale);

       $this->authorize('create partnership');

       // begin database transaction
       DB::beginTransaction();
       try {
           $partnership = Partnership::query()->create($request->all());

           // commit database
           DB::commit();
           // return success message
           return response()->json([
               'message' => Lang::get('crud.create'),
               'partnershipId' => $partnership->id
           ], 201);
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
    * @param $locale
    * @param Partnership $partnership
    * @return PartnershipEditResource|JsonResponse
    * @throws AuthorizationException
    */
   public function show($locale, Partnership $partnership): PartnershipEditResource|JsonResponse
   {
       App::setLocale($locale);

       $this->authorize('view partnership');

       try {
           return new PartnershipEditResource($partnership);
       } catch (Throwable $exception) {
           report($exception);
           return response()->json([
               'message' => Lang::get('crud.error')
           ], 404);
       }
   }

   /**
    * Edit article.
    *
    * @param $locale
    * @param Partnership $partnership
    * @return PartnershipEditResource|JsonResponse
    * @throws AuthorizationException
    */
   public function edit($locale, Partnership $partnership): PartnershipEditResource|JsonResponse
   {
       App::setLocale($locale);

       $this->authorize('update partnership');

       try {
           return new PartnershipEditResource($partnership);
       } catch (Throwable $exception) {
           report($exception);
           return response()->json([
               'message' => Lang::get('crud.error')
           ], 404);
       }
   }

   /**
    * Update the specified resource in storage.
    *
    * @param Request $request
    * @param $locale
    * @param Partnership $partnership
    * @return JsonResponse
    * @throws AuthorizationException
    */
   public function update(Request $request, $locale, Partnership $partnership): JsonResponse
   {
       App::setLocale($locale);

       $this->authorize('update partnership');

       // begin database transaction
       DB::beginTransaction();
       try {
           $partnership->update($request->all());
           // commit database
           DB::commit();

           // return success message
           return response()->json([
               'message' => Lang::get('crud.update')
           ]);
       } catch (Throwable $exception) {
           // log exception
           report($exception);
           // rollback database
           DB::rollBack();
           // return failed message
           return response()->json([
               'message' => Lang::get('crud.error'),
               'error' => $exception->getMessage()
           ], 400);
       }
   }

   /**
    * Remove the specified resource from storage.
    *
    * @param $locale
    * @param Partnership $partnership
    * @return JsonResponse
    * @throws AuthorizationException
    */
   public function destroy($locale, Partnership $partnership): JsonResponse
   {
       App::setLocale($locale);
       $this->authorize('delete partnership');

       // begin database transaction
       DB::beginTransaction();
       try {
           $partnership->delete();

           // commit changes
           DB::commit();
           return response()->json([
               'message' => Lang::get('crud.delete')
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
    * @param Partnership $partnership
     * @return JsonResponse
     */
    public function generateDynamicLinks($locale, Partnership $partnership): JsonResponse
    {
        $type = 'partnership';
        $referral = Referral::where('partnership_id', $partnership->id)
            ->where('type', $type);

        //Check already generated referral link of this partnership
        if($referral->count()){
            return response()->json([
                'message' => Lang::get('customer.referral_has_link'),
            ], 422);
        }

        $partnership['referral_type'] = $type;
        AddReferralDeepLink::dispatch($partnership);

        return response()->json([
            'message' => Lang::get('customer.referral_link'),
        ]);
    }
}
