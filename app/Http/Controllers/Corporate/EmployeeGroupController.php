<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\Corporate\EmployeeGroup;
use App\Models\Corporate\EmployeeGroupTranslation;
use App\Http\Resources\Corporate\EmployeeGroupResource;
use App\Http\Resources\Corporate\EmployeeGroupEditResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Throwable;

class EmployeeGroupController extends Controller
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

       $this->authorize('viewAny employee-group');

       $query = $request->query('query');
       $sortBy = $request->query('sortBy');
       $direction = $request->query('direction');
       $per_page = $request->query('per_page', 10);

       $employee_groups = EmployeeGroup::query()->latest();
       if ($query) {
           $employee_groups = $employee_groups->whereTranslationLike('name', '%' . $query . '%');
       }
       if ($sortBy) {
           $employee_groups = $employee_groups->orderBy($sortBy, $direction);
       }
       if ($per_page === '-1') {
           $results = $employee_groups->get();
           $employee_groups = new LengthAwarePaginator($results, $results->count(), -1);
       } else {
           $employee_groups = $employee_groups->paginate($per_page);
       }
       return EmployeeGroupResource::collection($employee_groups);
   }

   /**
    * Store a newly created resource in storage.
    *
    * @param $locale
    * @return JsonResponse
    * @throws AuthorizationException
    */
   public function store($locale): JsonResponse
   {
       App::setLocale($locale);

       $this->authorize('create employee-group');

       // begin database transaction
       DB::beginTransaction();
       try {
           $employee_group = EmployeeGroup::query()->create([
               'status' => 'inactive',
           ]);

           // commit database
           DB::commit();
           // return success message
           return response()->json([
               'message' => Lang::get('crud.create'),
               'employeeGroupId' => $employee_group->id
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
    * @param EmployeeGroup $employeeGroup
    * @return EmployeeGroupEditResource|JsonResponse
    * @throws AuthorizationException
    */
   public function show($locale, EmployeeGroup $employeeGroup): EmployeeGroupEditResource|JsonResponse
   {
       App::setLocale($locale);

       $this->authorize('view employee-group');

       try {
           return new EmployeeGroupEditResource($employeeGroup);
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
    * @param EmployeeGroup $employeeGroup
    * @return EmployeeGroupEditResource|JsonResponse
    * @throws AuthorizationException
    */
   public function edit($locale, EmployeeGroup $employeeGroup): EmployeeGroupEditResource|JsonResponse
   {
       App::setLocale($locale);

       $this->authorize('update employee-group');

       try {
           return new EmployeeGroupEditResource($employeeGroup);
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
    * @param EmployeeGroup $employeeGroup
    * @return JsonResponse
    * @throws AuthorizationException
    */
   public function update(Request $request, $locale, EmployeeGroup $employeeGroup): JsonResponse
   {
       App::setLocale($locale);
       $request->validate([
            'company' => 'required',
        ]);

       $this->authorize('update employee-group');

       // begin database transaction
       DB::beginTransaction();
       try {

            // Get company id
            if ($request->filled('company')) {
                $company = $request->get('company');
                $request->merge(['company_id' => $company['id']]);
            }

           $employeeGroup->update($request->all());
           
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
    * @param EmployeeGroup $employeeGroup
    * @return JsonResponse
    * @throws AuthorizationException
    */
   public function destroy($locale, EmployeeGroup $employeeGroup): JsonResponse
   {
       App::setLocale($locale);
       $this->authorize('delete employee-group');

       // begin database transaction
       DB::beginTransaction();
       try {
           $employeeGroup->delete();

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
     * Create slug for employee group.
     *
     * @param $locale
     * @param $title
     * @return JsonResponse
     */
    public function checkSlug($locale, $title): JsonResponse
    {
        try {
            $slug = Str::slug($title, '-', $locale);
            # slug repeat check
            $latest = EmployeeGroupTranslation::where('slug', '=', $slug)
                ->latest('id')
                ->value('slug');

            if ($latest) {
                $pieces = explode('-', $latest);
                $number = intval(end($pieces));
                $slug .= '-' . ($number + 1);
            }
            return response()->json([
                'slug' => $slug
            ]);
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse | EmployeeGroupResource
     */
    public function getEmployeeGroups(Request $request): JsonResponse | EmployeeGroupResource
    {
        try {

            $employee_groups = EmployeeGroup::with('translation')
                ->latest()
                ->where('status', '=', 'active')
                ->get();

                return response()->json([
                    'data' => $employee_groups
                ], 200);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

}
