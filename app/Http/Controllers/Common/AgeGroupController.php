<?php

namespace App\Http\Controllers\Common;

use App\Models\Common\AgeGroup;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryTreeResource;
use App\Http\Resources\Common\AgeGroupEditResource;
use App\Http\Resources\Common\AgeGroupResource;
use App\Models\Community\Post;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Throwable;

class AgeGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('viewAny age_group');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $age_groups = AgeGroup::latest();
        if ($query) {
            $age_groups = $age_groups->whereTranslationLike('name', '%' . $query . '%');
        }
        if ($sortBy) {
            $age_groups = $age_groups->orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $age_groups->get();
            $age_groups = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $age_groups = $age_groups->paginate($per_page);
        }


        return AgeGroupEditResource::collection($age_groups);
    }

    /**
     * Get all age_groups
     *
     * @return AnonymousResourceCollection
     */
    public function getAll()
    {
        $filters = AgeGroup::with('children')->whereIsRoot()->defaultOrder()->get();

        return CategoryTreeResource::collection($filters);
    }

    /**
     * Get all age_groups
     *
     * @return AnonymousResourceCollection
     */
    public function getAllAppAgeGroups()
    {
        $age_groups = AgeGroup::with('translations')
            ->where('status', '=', 'active')    
            ->get();

        return AgeGroupResource::collection($age_groups);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $locale
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthorizationException
     */
    public function store($locale)
    {
        App::setLocale($locale);

        $this->authorize('create age_group');

        // begin database transaction
        DB::beginTransaction();
        try {
            $age_group = AgeGroup::create();

            // commit changes
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.create'),
                'ageGroupId' => $age_group?->id
            ], 201);
        } catch (Throwable $exception) {
            report($exception);
            // rollback changes
            DB::rollBack();
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * Show age_group.
     *
     * @param $locale
     * @param AgeGroup $age_group
     * @return AgeGroupEditResource|JsonResponse
     */
    public function show($locale, AgeGroup $age_group)
    {
        App::setLocale($locale);

        try {

            return new AgeGroupEditResource($age_group);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * Edit age_group.
     *
     * @param $locale
     * @param AgeGroup $age_group
     * @return AgeGroupEditResource|JsonResponse
     */
    public function edit($locale, AgeGroup $age_group)
    {
        App::setLocale($locale);

        try {
            return new AgeGroupEditResource($age_group);
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
     * @param AgeGroup $age_group
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $locale, AgeGroup $age_group)
    {
        App::setLocale($locale);

        $this->authorize('update age_group');

        $this->validate($request, [
            'name' => 'required'
        ]);

        // begin database transaction
        DB::beginTransaction();
        try {
            $age_group->update($request->all());

            // commit changes
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.update')
            ], 200);
        } catch (Throwable $exception) {
            report($exception);
            // rollback changes
            DB::rollBack();
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $locale
     * @param AgeGroup $age_group
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($locale, AgeGroup $age_group)
    {
        App::setLocale($locale);

        $this->authorize('delete age_group');

        if(isset($age_group) && Post::where('age_group_id', '=', $age_group?->id)->count()){
            return response()->json([ 'message' => "Sorry, The age group using in post!"], 401);
        }

        // begin database transaction
        DB::beginTransaction();
        try {
            $age_group->forceDelete();

            // commit changes
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.delete')
            ], 200);
        } catch (Throwable $exception) {
            report($exception);
            // rollback changes
            DB::rollBack();
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

}
