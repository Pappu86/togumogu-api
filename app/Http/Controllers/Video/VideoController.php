<?php

namespace App\Http\Controllers\Video;

use App\Models\Video\Video;
use App\Models\Video\VideoTranslation;
use App\Http\Controllers\Controller;
use App\Http\Requests\Video\VideoRequest;
use App\Http\Resources\Video\VideoEditResource;
use App\Http\Resources\Video\VideoResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Throwable;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('viewAny video');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $videos = Video::latest();
        if ($query) {
            $videos = Video::whereTranslationLike('title', '%' . $query . '%');
        }
        if ($sortBy) {
            $videos = Video::orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $videos->get();
            $videos = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $videos = $videos->paginate($per_page);
        }
        return VideoResource::collection($videos);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store($locale)
    {
        App::setLocale($locale);

        $this->authorize('create video');

        // begin database transaction
        DB::beginTransaction();
        try {
            $video = Video::create([
                'user_id' => auth()->id(),
                'datetime' => now()
            ]);

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.create'),
                'videoId' => $video->id
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
     * @param Video $video
     * @return VideoEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show($locale, Video $video)
    {
        App::setLocale($locale);

        $this->authorize('view video');

        try {
            return new VideoEditResource($video);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * Edit video.
     *
     * @param $locale
     * @param Video $video
     * @return VideoEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function edit($locale, Video $video)
    {
        App::setLocale($locale);

        $this->authorize('update video');

        try {
            return new VideoEditResource($video);
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
     * @param VideoRequest $request
     * @param $locale
     * @param Video $video
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(VideoRequest $request, $locale, Video $video)
    {
        
        App::setLocale($locale);

        $this->authorize('update video');

        // begin database transaction
        DB::beginTransaction();
        try {

            $video->update($request->all());
            
            // Update video categories
            if ($request->filled('categories')) {
                $items = collect($request->input('categories'))->pluck('id');
                $video->categories()->sync($items);
            }

            if ($request->filled('tags')) {
                $items = collect($request->input('tags'))->pluck('id');
                $video->tags()->sync($items);
            }

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.update')
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
     * Remove the specified resource from storage.
     *
     * @param $locale
     * @param Video $video
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($locale, Video $video)
    {
        App::setLocale($locale);

        $this->authorize('delete video');

        // begin database transaction
        DB::beginTransaction();
        try {
            // delete video
            $video->delete();

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.trash')
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
     * Get trashed videos
     *
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function getTrashed(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('viewAny video');

        // $per_page = empty($request->query('per_page')) ? 10 : (int)$request->query('per_page');
        // $videos = Video::onlyTrashed()->latest()->paginate($per_page);

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $videos = Video::onlyTrashed();
        if ($query) {
            $videos = $videos->whereTranslationLike('title', '%' . $query . '%');
        }

        if ($sortBy) {
            $videos = $videos->orderBy($sortBy, $direction);
        } else {
            $videos = $videos->latest();
        }

        if ($per_page === '-1') {
            $results = $videos->get();
            $videos = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $videos = $videos->paginate($per_page);
        }

        return VideoResource::collection($videos);
    }

    /**
     * Restore all trashed videos
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function restoreTrashed(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('restore video');

        $ids = explode(',', $request->get('ids'));

        // begin database transaction
        DB::beginTransaction();
        try {
            if (isset($ids)) {
                Video::onlyTrashed()
                    ->whereIn('id', $ids)
                    ->restore();
            } else {
                Video::onlyTrashed()->restore();
            }

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.restore')
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
     * Restore single trashed video
     *
     * @param $locale
     * @param $id
     * @return mixed
     * @throws AuthorizationException
     */
    public function restoreSingleTrashed($locale, $id)
    {
        App::setLocale($locale);

        $this->authorize('restore video');

        // begin database transaction
        DB::beginTransaction();
        try {
            Video::onlyTrashed()
                ->where('id', '=', $id)
                ->restore();

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.restore')
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
     * Permanently delete all trashed videos
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceDelete(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('forceDelete video');

        $ids = explode(',', $request->get('ids'));

        // begin database transaction
        DB::beginTransaction();
        try {
            if (isset($ids)) {
                $videos = Video::onlyTrashed()
                    ->whereIn('id', $ids)
                    ->get();
            } else {
                $videos = Video::onlyTrashed()->get();
            }
            foreach ($videos as $video) {
                // delete tag
                $video->tags()->detach();
                // delete video
                $video->forceDelete();
            }

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.delete')
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
     * Permanently delete single trashed video
     *
     * @param $locale
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceSingleDelete($locale, $id)
    {
        App::setLocale($locale);

        $this->authorize('forceDelete video');

        // begin database transaction
        DB::beginTransaction();
        try {
            $video = Video::onlyTrashed()
                ->where('id', '=', $id)
                ->first();

            // delete tag
            $video->tags()->detach();
            // delete video
            $video->forceDelete();

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.delete')
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
     * Create slug for video.
     *
     * @param $locale
     * @param $title
     * @return JsonResponse
     */
    public function checkSlug($locale, $title)
    {
        try {
            $slug = Str::slug($title, '-', $locale);
            # slug repeat check
            $latest = VideoTranslation::where('slug', '=', $slug)
                ->latest('id')
                ->value('slug');

            if ($latest) {
                $pieces = explode('-', $latest);
                $number = intval(end($pieces));
                $slug .= '-' . ($number + 1);
            }
            return response()->json([
                'slug' => $slug
            ], 200);
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }
}
