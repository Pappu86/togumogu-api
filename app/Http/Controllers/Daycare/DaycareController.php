<?php

namespace App\Http\Controllers\Daycare;

use App\Jobs\DeepLink\AddDayCareDeepLink;
use App\Models\Daycare\Daycare;
use App\Models\Daycare\DaycareTranslation;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Daycare\DaycareEditResource;
use App\Http\Resources\Daycare\DaycareResource;
use App\Jobs\DeepLink\AddDayCareDeepLinkForFB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Throwable;

class DaycareController extends Controller
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

        $this->authorize('viewAny daycare');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $daycares = Daycare::query()->latest();
        if ($query) {
            $daycares = $daycares->whereTranslationLike('name', '%' . $query . '%');
        }
        if ($sortBy) {
            $daycares = Daycare::query()->orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $daycares->get();
            $daycares = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $daycares = $daycares->paginate($per_page);
        }
        return DaycareResource::collection($daycares);
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

        $this->authorize('create daycare');

        // begin database transaction
        DB::beginTransaction();
        try {
            $daycare = Daycare::query()->create([
                'user_id' => auth()->id(),
            ]);

            // Default insert rating for daycare
            $daycare->ratings()->create([
                'user_id' => auth()->id(),
                'facility' => '5',
                'security' => '5',
                'fee' => '5',
                'hygiene' => '5',
                'care_giving' => '5',
            ]);

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.create'),
                'daycareId' => $daycare->id
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
     * @param Daycare $daycare
     * @return DaycareEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show($locale, Daycare $daycare): DaycareEditResource|JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('view daycare');

        try {
            return new DaycareEditResource($daycare);
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
     * @param Daycare $daycare
     * @return DaycareEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function edit($locale, Daycare $daycare): DaycareEditResource|JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('update daycare');

        try {
            return new DaycareEditResource($daycare);
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
     * @param Daycare $daycare
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, $locale, Daycare $daycare): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('update daycare');

        // begin database transaction
        DB::beginTransaction();
        try {
            $daycare->update($request->all());

            // update images
            if ($request->filled('images')) {
                $daycare->images()->delete();
                $daycare->images()->createMany($request->get('images'));
            }

            // Update daycare categories
            if ($request->filled('categories')) {
                $items = collect($request->input('categories'))->pluck('id');
                $daycare->categories()->sync($items);
            }

            // Update features
            if ($request->filled('features')) {
                $features = $request->get('features');
                $data = array();

                foreach($features as $feature){
                    $data[$feature['id']] = ['active' => $feature['active']];
               }
                $daycare->features()->sync($data);
            }

            // Update rating
            if ($request->filled('ratings')) {
                $daycare->ratings()->updateOrCreate(['user_id' => auth()->id()], $request->get('ratings'));
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
     * @param Daycare $daycare
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($locale, Daycare $daycare): JsonResponse
    {
        App::setLocale($locale);
        $this->authorize('delete daycare');

        // begin database transaction
        DB::beginTransaction();
        try {
            $daycare->delete();

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
     * Create slug for daycare.
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
            $latest = DaycareTranslation::query()->where('slug', '=', $slug)
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
     * @return JsonResponse
     */
    public function generateDynamicLinks(): JsonResponse
    {
        Daycare::with(['translations'])
            ->where('status', '=', 'active')
            ->get()
            ->map(function ($daycare) {
                // generate dynamic link
                AddDayCareDeepLink::dispatch($daycare);
                AddDayCareDeepLinkForFB::dispatch($daycare);
            });

        return response()->json('Generated Successfully');
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

       $this->authorize('create daycare');

       // begin database transaction
       DB::beginTransaction();
       try {

        $daycares = $request->all();
        $user_id =  auth()->id();
        
        foreach ($daycares as $daycare) {
            $daycare['slug'] = $this->checkSlug($locale, $daycare['name'])->original['slug'];
            $daycare['user_id'] = $user_id;

            $new_daycare = new Daycare();
            $new_daycare->fill($daycare);
            $new_daycare->save();

            // Update daycare categories
            $categories = $daycare['categories'];
            if (isset($categories)) {
                $items = collect($categories)->pluck('id');
                $new_daycare->categories()->sync($items);
            }
        }
        
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

}
