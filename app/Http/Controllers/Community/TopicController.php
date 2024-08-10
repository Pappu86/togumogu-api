<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Http\Resources\Community\TopicEditResource;
use App\Http\Resources\Community\TopicResource;
use App\Http\Resources\CategoryTreeResource;
use App\Models\Community\Topic;
use App\Models\Community\TopicTranslation;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Throwable;

class TopicController extends Controller
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

        $this->authorize('viewAny topic');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $topics = Topic::with('children')->whereIsRoot()->defaultOrder();
        if ($query) {
            $topics = $topics->whereTranslationLike('name', '%' . $query . '%');
        }
        if ($sortBy) {
            $topics = $topics->orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $topics->get();
            $topics = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $topics = $topics->paginate($per_page);
        }

        return TopicResource::collection($topics);
    }

    /**
     * Get all topics
     *
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getAll($locale)
    {
        $this->authorize('viewAny topic');

        $topics = DB::table('topic as t')
            ->join('topic_translations as tt', 't.id', '=', 'tt.topic_id')
            ->select('t.id', 'tt.name')
            ->where('tt.locale', '=', $locale)
            ->get();

        return response()->json([
            'data' => $topics
        ], 200);
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

        $this->authorize('create topic');

        // begin database transaction
        DB::beginTransaction();
        try {
            $topic = Topic::create();

            // commit changes
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.create'),
                'topicId' => $topic->id
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
     * Show topic.
     *
     * @param $locale
     * @param Topic $topic
     * @return TopicEditResource|JsonResponse
     */
    public function show($locale, Topic $topic)
    {
        App::setLocale($locale);

        try {
            return new TopicEditResource($topic);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * Edit topic.
     *
     * @param $locale
     * @param Topic $topic
     * @return TopicEditResource|JsonResponse
     */
    public function edit($locale, Topic $topic)
    {
        App::setLocale($locale);

        try {
            return new TopicEditResource($topic);
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
     * @param Topic $topic
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $locale, Topic $topic)
    {
        App::setLocale($locale);

        $this->authorize('update topic');

        $translation = DB::table('topic_translations')->where('topic_id', '=', $topic->id)->where('locale', '=', $locale)->first();
        $translation_id = null;
        if ($translation) {
            $translation_id = $translation->id;
        }

        $this->validate($request, [
            'name' => 'required',
            'slug' => 'required|alpha_dash|unique:topic_translations,slug,' . $translation_id,
        ]);

        // begin database transaction
        DB::beginTransaction();
        try {
            // $slug = $request->get('slug');
            // $request->merge(['slug' => $slug]);

            $topic->update($request->all());

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
     * @param Topic $topic
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($locale, Topic $topic)
    {
        App::setLocale($locale);

        $this->authorize('delete topic');

        // begin database transaction
        DB::beginTransaction();
        try {
            $topic->delete();

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

    /**
     * Get trashed topics
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getTrashed(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('viewAny topic');

        $per_page = empty($request->query('per_page')) ? 10 : (int)$request->query('per_page');

        $topics = Topic::onlyTrashed()->latest()->paginate($per_page);

        return response()->json([
            'data' => $topics
        ], 200);
    }

    /**
     * Restore all trashed topics
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function restoreTrashed(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('restore topic');

        $ids = explode(',', $request->get('ids'));

        // begin database transaction
        DB::beginTransaction();
        try {
            if (isset($ids)) {
                Topic::onlyTrashed()
                    ->whereIn('id', $ids)
                    ->restore();
            } else {
                Topic::onlyTrashed()->restore();
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
     * Restore single trashed topic
     *
     * @param $locale
     * @param $id
     * @return mixed
     * @throws AuthorizationException
     */
    public function restoreSingleTrashed($locale, $id)
    {
        App::setLocale($locale);

        $this->authorize('restore topic');

        // begin database transaction
        DB::beginTransaction();
        try {
            Topic::onlyTrashed()
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
     * Permanently delete all trashed topics
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceDelete(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('forceDelete topic');

        $ids = explode(',', $request->get('ids'));

        // begin database transaction
        DB::beginTransaction();
        try {
            if (isset($ids)) {
                $topics = Topic::onlyTrashed()
                    ->whereIn('id', $ids)
                    ->get();
            } else {
                $topics = Topic::onlyTrashed()->get();
            }
            foreach ($topics as $topic) {
                // delete related posts
                $topic->posts()->delete();
                // delete topic
                $topic->forceDelete();
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
     * Permanently delete single trashed topic
     *
     * @param $locale
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceSingleDelete($locale, $id)
    {
        App::setLocale($locale);

        $this->authorize('forceDelete topic');

        // begin database transaction
        DB::beginTransaction();
        try {
            $topic = Topic::onlyTrashed()
                ->where('id', '=', $id);

            // delete related post
            $topic->posts()->delete();
            // delete topic
            $topic->forceDelete();

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
     * Rebuild topic parent children.
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function rebuildTree(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('update topic');

        // begin database transaction
        DB::beginTransaction();
        try {
            // rearrange topic
            Topic::rebuildTree($request->all());

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
     * Create slug for post.
     *
     * @param $locale
     * @param $title
     * @return string
     */
    public function checkSlug($locale, $title)
    {
        try {
            $slug = Str::slug($title, '-', $locale);
            # slug repeat check
            $latest = TopicTranslation::where('slug', '=', $slug)
                ->latest('id')
                ->value('slug');

            if ($latest) {
                $pieces = explode('-', $latest);
                $number = intval(end($pieces));
                $slug .= '-' . ($number + 1);
            }

            return response()->json([
                'slug' => $slug
            ]);;

        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // return failed message
            return null;
        }
    }

    /**
     * @param $locale
     * @return AnonymousResourceCollection
     */
    public function getAllAsTree($locale): AnonymousResourceCollection
    {
        App::setLocale($locale);

        $topics = Topic::with(['translations', 'children' => function ($query) {
            $query->with(['translations', 'children' => function ($q) {
                $q->with(['translations', 'children'])
                    ->where('status', '=', 'active')
                    ->defaultOrder();
            }])
                ->where('status', '=', 'active')
                ->defaultOrder();
        }])
            ->where('status', '=', 'active')
            ->whereIsRoot()->defaultOrder()->get();

        return CategoryTreeResource::collection($topics);
    }

}
