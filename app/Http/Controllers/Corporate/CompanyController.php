<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\Corporate\Company;
use App\Models\Corporate\CompanyTranslation;
use App\Http\Resources\Corporate\CompanyResource;
use App\Http\Resources\Corporate\CompanyEditResource;
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
use Throwable;

class CompanyController extends Controller
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

       $this->authorize('viewAny company');

       $query = $request->query('query');
       $sortBy = $request->query('sortBy');
       $direction = $request->query('direction');
       $per_page = $request->query('per_page', 10);

       $companies = Company::query()->latest();
       if ($query) {
           $companies = $companies->whereTranslationLike('name', '%' . $query . '%');
       }
       if ($sortBy) {
           $companies = $companies->orderBy($sortBy, $direction);
       }
       if ($per_page === '-1') {
           $results = $companies->get();
           $companies = new LengthAwarePaginator($results, $results->count(), -1);
       } else {
           $companies = $companies->paginate($per_page);
       }
       return CompanyResource::collection($companies);
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

       $this->authorize('create company');

       // begin database transaction
       DB::beginTransaction();
       try {
           $company = Company::query()->create([
               'status' => 'inactive',
           ]);

           // commit database
           DB::commit();
           // return success message
           return response()->json([
               'message' => Lang::get('crud.create'),
               'companyId' => $company->id
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
    * @param Company $company
    * @return CompanyEditResource|JsonResponse
    * @throws AuthorizationException
    */
   public function show($locale, Company $company): CompanyEditResource|JsonResponse
   {
       App::setLocale($locale);

       $this->authorize('view company');

       try {
           return new CompanyEditResource($company);
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
    * @param Company $company
    * @return CompanyEditResource|JsonResponse
    * @throws AuthorizationException
    */
   public function edit($locale, Company $company): CompanyEditResource|JsonResponse
   {
       App::setLocale($locale);

       $this->authorize('update company');

       try {
           return new CompanyEditResource($company);
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
    * @param Company $company
    * @return JsonResponse
    * @throws AuthorizationException
    */
   public function update(Request $request, $locale, Company $company): JsonResponse
   {
       App::setLocale($locale);

       $this->authorize('update company');

       // begin database transaction
       DB::beginTransaction();
       try {
           $company->update($request->all());

           // update images
           if ($request->filled('images')) {
               $company->images()->delete();
               $company->images()->createMany($request->get('images'));
           }

           // Update company categories
           if ($request->filled('categories')) {
               $items = collect($request->input('categories'))->pluck('id');
               $company->categories()->sync($items);
           }

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
    * @param Company $company
    * @return JsonResponse
    * @throws AuthorizationException
    */
   public function destroy($locale, Company $company): JsonResponse
   {
       App::setLocale($locale);
       $this->authorize('delete company');

       // begin database transaction
       DB::beginTransaction();
       try {
           $company->delete();

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
     * @param Request $request
     * @return JsonResponse | AnonymousResourceCollection
     */
    public function getCompanies(Request $request): JsonResponse | AnonymousResourceCollection
    {
        try {

            $companies = Company::with('translation')
            ->latest()
            ->where('status', '=', 'active')
            ->get();

            return response()->json([
                'data' => $companies
            ], 200);

        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

}
