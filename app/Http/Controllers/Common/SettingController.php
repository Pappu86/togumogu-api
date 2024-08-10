<?php

namespace App\Http\Controllers\Common;

use App\Models\Setting;
use App\Http\Controllers\Controller;
use App\Http\Resources\Common\ContactUsSettingResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;

class SettingController extends Controller
{
    /**
     * Get all key value of settings.
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getSettings()
    {
        $this->authorize('view setting');

        $collections = DB::table('settings')
            ->select('id', 'key', 'label', 'value', 'updated_at', 'category', 'type', 'status')
            ->get();

        $settings = collect($collections)->groupBy('category')->toArray();

        return response()->json($settings, 200);
    }
    
    /**
     * Get all key value of settings.
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getContactUsSettings()
    {
        $settings = DB::table('settings')
            ->where('status', '=', 'active')
            ->where('category', '=', 'contact')
            ->pluck('value', 'key');

        return new ContactUsSettingResource($settings);
    }

    /**
     * Get all key value of settings.
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getOfferSettings()
    {
        $settings = DB::table('settings')
            ->where('status', '=', 'active')
            ->where('category', '=', 'offer')
            ->pluck('value', 'key');

        return response()->json([
            'data' => [
                'offer_description_bn' => $settings['offer_description_bn'],
                'offer_description_en' => $settings['offer_description_en'],
            ]
        ], 200);
    }

    /**
     * Update setting.
     *
     * @param Request $request
     * @param Setting $setting
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException|\Exception
     */
    public function update(Request $request, Setting $setting)
    {
        $this->authorize('update setting');

        $data = $this->validate($request, [
            'label' => 'required',
            'value' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $setting->update($data);

            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.update')
            ], 200);
        } catch (\Exception $exception) {
            report($exception);
            DB::rollBack();

            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * Update image settings.
     *
     * @param Request $request
     * @param Setting $setting
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException|\Exception
     */
    public function updateImage(Request $request, Setting $setting)
    {
        $this->authorize('update setting');

        $this->validate($request, [
            'logo' => 'nullable|image',
            'small_logo' => 'nullable|image',
            'favicon' => 'nullable|mimes:ico,png',
            'label' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $key = $request->get('key');
            $file = $request->file($key);
            $setting->addMedia($file)
                ->toMediaCollection($key);
            $image = env('APP_URL') . $setting->getFirstMediaUrl($key);

            $setting->update([
                'label' => $request->get('label'),
                'value' => $image
            ]);

            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.update')
            ], 200);

        } catch (\Exception $exception) {
            report($exception);
            DB::rollBack();

            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * Update file setting.
     *
     * @param Request $request
     * @param Setting $setting
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException|\Exception
     */
    public function updateFile(Request $request, Setting $setting)
    {
        $this->authorize('update setting');
        $key = $request->get('key');

        $this->validate($request, [
            $key => 'nullable|file|mimetypes:application/json,text/plain',
            'label' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $file = $request->file($key);

            $setting->addMedia($file)
                ->toMediaCollection($key);

            $uploadedFile = env('APP_URL') . $setting->getFirstMediaUrl($key);

            $setting->update([
                'label' => $request->get('label'),
                'value' => $uploadedFile
            ]);
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.update')
            ], 200);

        } catch (\Exception $exception) {
            report($exception);
            DB::rollBack();

            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }
}
