<?php

namespace App\Http\Controllers\Child;

use Carbon\Carbon;
use App\Http\Resources\Child\ChildResource;
use App\Models\Child\Child;
use App\Models\Child\Hospital;
use App\Models\Child\School;
use App\Models\Child\ChildClass;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class ChildAppController extends Controller
{
   
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $customer = Auth::user();
        $now = Carbon::now();
        $parent_status = $request->get('child_status');

        //Validations
        $request->validate([
            'name' => 'nullable',
            'parent_type' => 'required',
            'child_status' => Rule::requiredIf(function () use ($request) {
                return $request->get('parent_type') !== 'other';
            }),
            'date_of_birth' => Rule::requiredIf(function () use ($request) {
                return $request->get('child_status') === 'parent';
            }),
            'expecting_date' => Rule::requiredIf(function () use ($request) {
                return $request->get('child_status') === 'expecting';
            }),
        ]);

        $date_of_birth = $request->get('date_of_birth');
        $expecting_date = $request->get('expecting_date');

        //Compare DOB with current date of child
        if($parent_status === 'parent' && $now->lte(Carbon::parse($date_of_birth))) {
            return response()->json([ 'message' => Lang::get('customer.dob_validation') ]);
        };

        //Compare EDD with current date of child
        if($parent_status === 'expecting' && $now->gt(Carbon::parse($expecting_date))) {
            return response()->json([ 'message' => Lang::get('customer.edd_validation') ]);
        };

        // Date format
        if ($date_of_birth) {
            $date_of_birth = Carbon::parse($date_of_birth);
        }

        if ($expecting_date) {
            $expecting_date = Carbon::parse($expecting_date);
        }

        // begin database transaction
        DB::beginTransaction();
        try {

            // Insert document in customer/parent children table
            $customer->children()->create([
                'name' => $request->get('name'),
                'gender' => $request->get('gender')?:'',
                'date_of_birth' => $date_of_birth ? $date_of_birth : null,
                'expecting_date' => $expecting_date ? $expecting_date : null,
                'parent_status' => $parent_status,
                'is_default' => $customer->children()->count()?0:1,
            ]);

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.create')
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
     * @param Request $request
     * @param Child $child
     * @return ChildResource|JsonResponse
     */
    public function updateProfile(Request $request, Child $child): ChildResource|JsonResponse
    {

        DB::beginTransaction();
        try {

            // Hospital add or update information in child
            $doctor_id = $request->get('doctor_id');
            if (isset($doctor_id)) {
                $doctor_ids = $child->doctor_id?json_decode($child->doctor_id):[];
                array_push($doctor_ids, $doctor_id);
                $doctor_ids = array_unique($doctor_ids);

                $request->merge(['doctor_id' => $doctor_ids]);
            }

            // Hospital add or update information in child
            $hospital_name = $request->get('birth_hospital');
            if (isset($hospital_name)) {
                $hospital_info = Hospital::query()
                ->where('id', $hospital_name)
                ->orWhereTranslation('name', $hospital_name)
                ->first();
                $hospital_id = $hospital_info?->id ?: null;

                if (!$hospital_id) {
                    $new_hospital = Hospital::create([ 'name' => $hospital_name, 'status' => 'active' ]);
                    $hospital_id = $new_hospital->id;
                }

                $request->merge(['birth_hospital_id' => $hospital_id]);
            }

            // Class add or update information in child
            $class_name = $request->get('class');
            if (isset($class_name)) {
                $class_info = ChildClass::query()
                ->where('id', $class_name)
                ->orWhereTranslation('name', $class_name)
                ->first();
                $child_class_id = $class_info?->id ?: null;

                if (!$child_class_id) {
                    $new_class = ChildClass::create([ 'name' => $class_name, 'status' => 'active' ]);
                    $child_class_id = $new_class->id;
                }

                $request->merge(['child_class_id' => $child_class_id]);
            }

            // School add or update information in child
            $school_name = $request->get('school');
            if (isset($school_name)) {
                $school_info = School::query()
                ->where('id', $school_name)
                ->orWhereTranslation('name', $school_name)
                ->first();
                $school_id = $school_info?->id ?: null;

                if (!$school_id) {
                    $new_school = School::create([ 'name' => $school_name, 'status' => 'active' ]);
                    $school_id = $new_school->id;
                }

                $request->merge(['school_id' => $school_id]);
            }
            
            // Update child default options
            $child_default = $request->get('is_default');
            if(isset($child_default)) {
                $customer = Auth::user();
                $total_children = $customer->children()->count();
   
                if($child_default === 1 && $total_children>1) {
                    $customer->children()->whereNotIn('id',[$child->id])->update(['is_default' => 0]);
                } else if($child_default === 0 && $total_children === 1) {
                    $request->merge([ 'is_default' => 1 ]);
                }
            }
            
            $now = Carbon::now();
            $dateOfBirth = $request->get('date_of_birth');
            $expectingDate = $request->get('expecting_date');
            
            //Compare DOB with current date of child
            if(isset($dateOfBirth) && $now->lte(Carbon::parse($dateOfBirth))) {
                return response()->json([ 'message' => Lang::get('customer.dob_validation') ]);
            };
    
            //Compare EDD with current date of child
            if(isset($expectingDate) && $now->gt(Carbon::parse($expectingDate))) {
                return response()->json([ 'message' => Lang::get('customer.edd_validation') ]);
            };

            // Finally child data update
            $child->update($request->all());

            DB::commit();
            // return success message
            return ChildResource::make($child);

        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // rollback database
            DB::rollBack();
            // return failed message
            return response()->json([
                'error' => $exception->getMessage(),
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * @param Child $child
     * @return ChildResource
     */
    public function getSingleChild(Child $child): ChildResource
    {
        return ChildResource::make($child);
    }

    /**
     * @param Child $child
     * @return ChildResource|JsonResponse
     */
    public function removeDoctor(Request $request, Child $child): ChildResource|JsonResponse
    {
       
        DB::beginTransaction();
        try {

            // Hospital add or update information in child
            $doctor_id = $request->get('doctor_id');
            if (isset($doctor_id)) {
                $doctor_ids = $child->doctor_id?json_decode($child->doctor_id):[];
                $new_doctor_ids = [];
                foreach ($doctor_ids as $id) {
                  if($id !== (int)$doctor_id) array_push($new_doctor_ids, $id);
                }
                $new_doctor_ids = array_unique($new_doctor_ids);
                $request->merge(['doctor_id' => $new_doctor_ids]);
            }

            $child->update($request->all());

            DB::commit();
            // return success message
            return ChildResource::make($child);

        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // rollback database
            DB::rollBack();
            // return failed message
            return response()->json([
                'error' => $exception->getMessage(),
                'message' => Lang::get('crud.error')
            ], 400);
        }

    }


    /**
     * Upload customer avatar.
     *
     * @param Request $request
     * @param Child $child
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException|\Exception
     */
    public function updateAvatar(Request $request, Child $child)
    {
        $this->validate($request, [
            'avatar' => 'required|image'
        ]);
        // begin database transaction
        DB::beginTransaction();
        try {
            $image = $request->file('avatar');
            $url = $child->addMedia($image)->toMediaCollection('avatar')->getFullUrl();
            // update customer
            $child->update([
                'avatar' => $url
            ]);
            // commit changes
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.update'),
                'data' => ChildResource::make($child)
            ], 200);
        } catch (\Exception $exception) {
            // log exception
            report($exception);
            // rollback changes
            DB::rollBack();
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * @param Request $request
     * @param Child $child
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function singleDelete(Request $request, Child $child): JsonResponse
    {

        // begin database transaction
        DB::beginTransaction();
        try {

            //Default child not deleted
            $default_id = $request->get('default_child_id');
            if(isset($default_id) && $default_id != $child->id) {
                DB::table('children')
                ->where('id', '=', $default_id)
                ->where('parent_id', '=', $child->parent_id)
                ->update(["is_default" => 1]);
            };
            
            // Soft delete with remove default of child
            $child->update(["is_default" => 0]);
            $child->delete();

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
