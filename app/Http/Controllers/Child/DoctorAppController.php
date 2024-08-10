<?php

namespace App\Http\Controllers\Child;

use App\Http\Controllers\Controller;
use App\Http\Resources\Child\DoctorResource;
use App\Models\Child\Doctor;
use App\Models\Child\Hospital;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class DoctorAppController extends Controller
{

  /**
     * Store a newly created resource in storage.
     *
     * @param $locale
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->validate($request, [
            'name' => 'required|min:5',
            'department' => 'required',
            'hospital' => 'required',
        ]);

        $doctor_name = $request->get('name');

        // begin database transaction
        DB::beginTransaction();
        try {

            $hospital_name = $request->get('hospital');
            if (isset($hospital_name)) {
                $hospital_info = Hospital::query()
                    ->where('id', $hospital_name)
                    ->orWhereTranslation('name', $hospital_name)
                    ->first();
                $hospital_id = $hospital_info?->id ?: null;

                if (!$hospital_id) {
                    $new_hospital = Hospital::create([ 'name' => $hospital_name, 'status' => 'active' ]);
                    $hospital_id = $new_hospital->id;
                } else {

                    // check existing doctor name, department and hospital
                   $existing_doctor = Doctor::query()
                        ->where('department', '=', $request->get('department'))
                        ->where('hospital_id', '=', $hospital_id)
                        ->whereTranslation('name', $doctor_name)
                        ->first();

                    if(isset($existing_doctor)) {
                        return response()->json([
                            'message' => "Already `$doctor_name` exist!",
                            'doctor' => $existing_doctor->id,
                        ], 200);
                    }
                }

                $request->merge(['hospital_id' => $hospital_id]);
            }

            $doctor = Doctor::create($request->all());

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.create'),
                'doctor' => $doctor->id
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
     * @param Request $request
     * @return JsonResponse | AnonymousResourceCollection
     */
    public function getDoctors(Request $request, $locale): JsonResponse | AnonymousResourceCollection
    {

        App::setLocale($locale);

        try {

            $limit = $request->query('limit') ?: '5';
            $sort = $request->query('short');
            $query = $request->query('query')?:'';

            $doctor = Doctor::with('translations')
                ->whereTranslationLike('name', '%' . $query . '%')
                ->where('status', '=', 'active');

            $doctor = $doctor->paginate($limit);
            $doctor->appends([
                'limit' => $limit,
                'sort' => $sort,
            ]);

            return DoctorResource::collection($doctor);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

}
