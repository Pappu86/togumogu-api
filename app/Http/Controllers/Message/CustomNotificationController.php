<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Models\Message\CustomNotification;
use App\Http\Resources\Message\CustomNotificationResource;
use App\Http\Resources\Message\CustomNotificationEditResource;
use App\Jobs\Notification\SendCustomNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Notifications\Notification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Throwable;

class CustomNotificationController extends Controller
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

       $this->authorize('viewAny notification');

       $query = $request->query('query');
       $sortBy = $request->query('sortBy');
       $direction = $request->query('direction');
       $per_page = $request->query('per_page', 10);
       $notificationType = $request->query('notificationType', 'normal');

       $notifications = CustomNotification::query()->latest()
                        ->where('notification_type', $notificationType);
       if ($query) {
           $notifications = $notifications->whereLike('name', '%' . $query . '%');
       }
       if ($sortBy) {
           $notifications = $notifications->orderBy($sortBy, $direction);
       }
       if ($per_page === '-1') {
           $results = $notifications->get();
           $notifications = new LengthAwarePaginator($results, $results->count(), -1);
       } else {
           $notifications = $notifications->paginate($per_page);
       }
       return CustomNotificationResource::collection($notifications);
   }

   /**
    * Store a newly created resource in storage.
    * @param Request $request
    * @param $locale
    * @return JsonResponse
    * @throws AuthorizationException
    */
   public function store(Request $request, $locale): JsonResponse
   {
       App::setLocale($locale);

       $this->authorize('create notification');

       $notificationType = $request->query('notificationType', 'normal');

       // begin database transaction
       DB::beginTransaction();
       try {
           $notification = CustomNotification::query()->create([
               'status' => 'inactive',
               'process_status' => 'draft',
               'notification_type' => $notificationType,
           ]);

           // commit database
           DB::commit();
           // return success message
           return response()->json([
               'message' => Lang::get('crud.create'),
               'notificationId' => $notification->id
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
    * @param CustomNotification $notification
    * @return CustomNotificationEditResource|JsonResponse
    * @throws AuthorizationException
    */
   public function show($locale, CustomNotification $notification): CustomNotificationEditResource|JsonResponse
   {
       App::setLocale($locale);

       $this->authorize('view notification');

       try {
           return new CustomNotificationEditResource($notification);
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
    * @param CustomNotification $notification
    * @return CustomNotificationEditResource|JsonResponse
    * @throws AuthorizationException
    */
   public function edit($locale, CustomNotification $notification): CustomNotificationEditResource|JsonResponse
   {
       App::setLocale($locale);

       $this->authorize('update notification');

       try {
           return new CustomNotificationEditResource($notification);
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
    * @param CustomNotification $notification
    * @return JsonResponse
    * @throws AuthorizationException
    */
   public function update(Request $request, $locale, CustomNotification $notification): JsonResponse
   {
       App::setLocale($locale);

       $this->authorize('update notification');
       $status = $request->get('status');

       // begin database transaction
       DB::beginTransaction();
       try {

            if(isset($status) && $status === 'active') {
                $request->merge(['process_status' => 'processing']);
            }

           $is_updated = $notification->update($request->all());

           // commit database
           DB::commit();
           if($is_updated) {
                $notification = CustomNotification::where('id', '=', $notification['id'])->first();
                if(isset($notification) && $notification->status === 'active' && $notification->scheduling_type === 'now') {
                   Log::info("Start custom notification sending...");
                    SendCustomNotification::dispatch($notification);
                }
           }

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
    * @param CustomNotification $notification
    * @return JsonResponse
    * @throws AuthorizationException
    */
   public function destroy($locale, CustomNotification $notification): JsonResponse
   {
       App::setLocale($locale);
       $this->authorize('delete notification');

       // begin database transaction
       DB::beginTransaction();
       try {
           $notification->delete();

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

}
