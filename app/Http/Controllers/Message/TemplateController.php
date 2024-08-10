<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Models\Message\Template;
use App\Http\Resources\Message\TemplateResource;
use App\Http\Resources\Message\TemplateEditResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Throwable;

class TemplateController extends Controller
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

       $this->authorize('viewAny template');

       $query = $request->query('query');
       $sortBy = $request->query('sortBy');
       $direction = $request->query('direction');
       $per_page = $request->query('per_page', 10);
       $type = $request->query('type');

       $templates = Template::query()->latest();
       if ($query) {
           $templates = $templates->whereTranslationLike('title', '%' . $query . '%');
       }
       
       if ($sortBy) {
           $templates = $templates->orderBy($sortBy, $direction);
       }
        
       if ($type) {
           $templates = $templates->where("type", $type);
       }

       if ($per_page === '-1') {
           $results = $templates->get();
           $templates = new LengthAwarePaginator($results, $results->count(), -1);
       } else {
           $templates = $templates->paginate($per_page);
       }
       return TemplateResource::collection($templates);
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
           $employee_group = Template::query()->create([
               'status' => 'inactive',
           ]);

           // commit database
           DB::commit();
           // return success message
           return response()->json([
               'message' => Lang::get('crud.create'),
               'templateId' => $employee_group->id
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
    * @param Template $template
    * @return TemplateEditResource|JsonResponse
    * @throws AuthorizationException
    */
   public function show($locale, Template $template): TemplateEditResource|JsonResponse
   {
       App::setLocale($locale);

       $this->authorize('view template');

       try {
           return new TemplateEditResource($template);
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
    * @param Template $template
    * @return TemplateEditResource|JsonResponse
    * @throws AuthorizationException
    */
   public function edit($locale, Template $template): TemplateEditResource|JsonResponse
   {
       App::setLocale($locale);

       $this->authorize('update template');

       try {
           return new TemplateEditResource($template);
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
    * @param Template $template
    * @return JsonResponse
    * @throws AuthorizationException
    */
   public function update(Request $request, $locale, Template $template): JsonResponse
   {
       App::setLocale($locale);
       $request->validate([
            'name' => 'required',
            'subject' => 'required',
            'content' => 'required',
        ]);

       $this->authorize('update template');

       // begin database transaction
       DB::beginTransaction();
       try {

           $template->update($request->all());
           
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
    * @param Template $template
    * @return JsonResponse
    * @throws AuthorizationException
    */
   public function destroy($locale, Template $template): JsonResponse
   {
       App::setLocale($locale);
       $this->authorize('delete template');

       // begin database transaction
       DB::beginTransaction();
       try {
           $template->delete();

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
    public function getTemplates(Request $request): JsonResponse | AnonymousResourceCollection
    {
        try {

            $templates = Template::with('translation')
                ->latest()
                ->where('status', '=', 'active')
                ->get();

            return response()->json([
                'data' => TemplateResource::collection($templates)
            ], 200);

        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

}
