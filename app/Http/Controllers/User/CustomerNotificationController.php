<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\CustomerNotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;
use Throwable;

class CustomerNotificationController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function getNotifications(): JsonResponse
    {

        $customer = Auth::user();
        $notifications = $customer->notifications()->latest()->get();
        return response()->json([
            'data' => CustomerNotificationResource::collection($notifications)
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getUnreadNotifications(): JsonResponse
    {

        $customer = Auth::user();

        $notifications = $customer->unreadNotifications()->latest()->get();

        return response()->json([
            'data' => $notifications
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function markAsReadNotifications(): JsonResponse
    {

        $customer = Auth::user();

        DB::beginTransaction();
        try {
            $customer->unreadNotifications->markAsRead();

            DB::commit();

            return response()->json([
                'message' => Lang::get('crud.update')
            ]);
        } catch (Throwable $exception) {
            report($exception);
            DB::rollBack();

            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @return JsonResponse
     */
    public function deleteNotifications(): JsonResponse
    {

        $customer = Auth::user();
        DB::beginTransaction();
        try {
            $customer->notifications()->delete();

            DB::commit();

            return response()->json([
                'message' => Lang::get('crud.delete')
            ]);
        } catch (Throwable $exception) {
            report($exception);
            DB::rollBack();

            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function deleteNotification($id): JsonResponse
    {
        $customer = Auth::user();
        DB::beginTransaction();
        try {
            $customer->notifications()
                ->where('id', $id)
                ->delete();

            DB::commit();

            return response()->json([
                'message' => Lang::get('crud.delete')
            ]);
        } catch (Throwable $exception) {
            report($exception);
            DB::rollBack();

            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function markAsReadNotification($id): JsonResponse
    {
        $customer = Auth::user();
        DB::beginTransaction();
        try {
           $customer->notifications()
                ->where('id', $id)
                ->update(['read_at' => now()]);

            DB::commit();

            return response()->json([
                'message' => Lang::get('crud.update')
            ]);
        } catch (Throwable $exception) {
            report($exception);
            DB::rollBack();

            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @return JsonResponse
     */
    public function markAsReadAllNotification(): JsonResponse
    {
        $customer = Auth::user();
        DB::beginTransaction();
        try {
            $customer->unreadNotifications->markAsRead();
            DB::commit();

            return response()->json([
                'message' => Lang::get('crud.update')
            ]);
        } catch (Throwable $exception) {
            report($exception);
            DB::rollBack();

            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function markAsUnreadNotification($id): JsonResponse
    {
        $customer = Auth::user();
        DB::beginTransaction();
        try {
            $customer->notifications()
                ->where('id', $id)
                ->update(['read_at' => null]);

            DB::commit();

            return response()->json([
                'message' => Lang::get('crud.update')
            ]);
        } catch (Throwable $exception) {
            report($exception);
            DB::rollBack();

            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
        }
    }
}