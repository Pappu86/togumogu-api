<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\Corporate\Employee;
use App\Http\Resources\Corporate\EmployeeResource;
use App\Http\Resources\Corporate\EmployeeEditResource;
use App\Http\Resources\Corporate\EmployeeApiResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Rules\Mobile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class EmployeeController extends Controller
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

       $this->authorize('viewAny employee');

       $query = $request->query('query');
       $sortBy = $request->query('sortBy');
       $group_id = $request->query('group_id');
       $direction = $request->query('direction');
       $per_page = $request->query('per_page', 10);

       $employees = Employee::query()->latest();

        if ($query) {
           $employees = $employees->where('name', 'like', '%' . $query . '%')
            ->orWhere('phone', 'like', '%' . $query . '%')
            ->orWhere('email', 'like', '%' . $query . '%');
        }

        if ($group_id) {
            $employees = $employees->where('group_id', '=', $group_id);
        }

       if ($sortBy) {
           $employees = $employees->orderBy($sortBy, $direction);
       }

       if ($per_page === '-1') {
           $results = $employees->get();
           $employees = new LengthAwarePaginator($results, $results->count(), -1);
       } else {
           $employees = $employees->paginate($per_page);
       }
       return EmployeeResource::collection($employees);
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

       $this->authorize('create employee');

        $request->validate([
            'name' => 'required',
            'email' => 'nullable|email|unique:employees,email,' . $request->id,
            'phone' => ['required', new Mobile(), 'unique:employees,phone,' . $request->id],
        ]);

       // begin database transaction
       DB::beginTransaction();
       try {
           $employee = Employee::query()->create($request->all());

           // commit database
           DB::commit();
           // return success message
           return response()->json([
               'message' => Lang::get('crud.create'),
               'employeeId' => $employee->id
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
    * Store a newly created resource in storage.
    *
    * @param $request
    * @param $locale
    * @return JsonResponse
    * @throws AuthorizationException
    */
   public function createBulk(Request $request, $locale): JsonResponse
   {
       App::setLocale($locale);

       $this->authorize('create employee');

       // begin database transaction
       DB::beginTransaction();
       try {
       
            $employees = Employee::insertOrIgnore($request->all());

           // commit database
           DB::commit();
           // return success message
           return response()->json([
               'message' => Lang::get('crud.create'),
           ], 200);
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
    * @param Employee $employee
    * @return EmployeeEditResource|JsonResponse
    * @throws AuthorizationException
    */
   public function show($locale, Employee $employee): EmployeeEditResource|JsonResponse
   {
       App::setLocale($locale);

       $this->authorize('view employee');

       try {
           return new EmployeeEditResource($employee);
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
    * @param Employee $employee
    * @return EmployeeEditResource|JsonResponse
    * @throws AuthorizationException
    */
   public function edit($locale, Employee $employee): EmployeeEditResource|JsonResponse
   {
       App::setLocale($locale);

       $this->authorize('update employee');

       try {
           return new EmployeeEditResource($employee);
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
    * @param Employee $employee
    * @return JsonResponse
    * @throws AuthorizationException
    */
   public function update(Request $request, $locale, Employee $employee): JsonResponse
   {
       App::setLocale($locale);

       $this->authorize('update employee');

       $request->validate([
            'name' => 'required',
            'email' => 'nullable|email|unique:employees,email,' . $employee->id,
            'phone' => ['required', new Mobile(), 'unique:employees,phone,' . $employee->id],
        ]);

       // begin database transaction
       DB::beginTransaction();
       try {
           $employee->update($request->all());
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
    * @param Employee $employee
    * @return JsonResponse
    * @throws AuthorizationException
    */
   public function destroy($locale, Employee $employee): JsonResponse
   {
       App::setLocale($locale);
       $this->authorize('delete employee');

       // begin database transaction
       DB::beginTransaction();
       try {
           $employee->delete();

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
    * @param Employee $employee
    * @return EmployeeApiResource|JsonResponse
    * @throws AuthorizationException
    */
    public function getSingleEmployee($locale, Employee $employee): EmployeeApiResource|JsonResponse
    {
        App::setLocale($locale);
 
        try {
            return new EmployeeApiResource($employee);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }
 

}
