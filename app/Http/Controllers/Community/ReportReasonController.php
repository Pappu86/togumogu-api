<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Http\Resources\Community\TopicEditResource;
use App\Http\Resources\Community\ReportReasonResource;
use App\Http\Resources\Community\ReportReasonEditResource;
use App\Models\Community\ReportReason;
use App\Models\Community\ReportReasonTranslation;
use App\Models\Community\Topic;
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

class ReportReasonController extends Controller
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

        $this->authorize('viewAny report_reason');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $reportReasons = ReportReason::latest();
        if ($query) {
            $reportReasons = $reportReasons->whereTranslationLike('title', '%' . $query . '%');
        }
        if ($sortBy) {
            $reportReasons = $reportReasons->orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $reportReasons->get();
            $reportReasons = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $reportReasons = $reportReasons->paginate($per_page);
        }

        return ReportReasonResource::collection($reportReasons);
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

        $this->authorize('create report_reason');

        // begin database transaction
        DB::beginTransaction();
        try {
            $reportReason = ReportReason::create();

            // commit changes
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.create'),
                'reportReasonId' => $reportReason->id
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
     * @param ReportReason $reportReason
     * @return TopicEditResource|JsonResponse
     */
    public function show($locale, ReportReason $reportReason)
    {
        App::setLocale($locale);

        try {
            return new ReportReasonEditResource($reportReason);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * Edit reportReason.
     *
     * @param $locale
     * @param ReportReason $reportReason
     * @return TopicEditResource|JsonResponse
     */
    public function edit($locale, ReportReason $reportReason)
    {
        App::setLocale($locale);

        try {
            return new TopicEditResource($reportReason);
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
     * @param ReportReason $reportReason
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $locale, ReportReason $reportReason)
    {
        App::setLocale($locale);

        $this->authorize('update report_reason');

        $translation = DB::table('report_reason_translations')->where('report_reason_id', '=', $reportReason->id)->where('locale', '=', $locale)->first();
        $translation_id = null;
        if ($translation) {
            $translation_id = $translation->id;
        }

        $this->validate($request, [
            'title' => 'required',
            'slug' => 'required|alpha_dash|unique:report_reason_translations,slug,' . $translation_id,
        ]);

        // begin database transaction
        DB::beginTransaction();
        try {

            $reportReason->update($request->all());

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
     * @param ReportReason $reportReason
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($locale, ReportReason $reportReason)
    {
        App::setLocale($locale);

        $this->authorize('delete report_reason');

        // begin database transaction
        DB::beginTransaction();
        try {
            $reportReason->delete();

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

        $this->authorize('forceDelete report_reason');

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
            $latest = ReportReasonTranslation::where('slug', '=', $slug)
                ->latest('id')
                ->value('slug');

            if ($latest) {
                $pieces = explode('-', $latest);
                $number = intval(end($pieces));
                $slug .= '-' . ($number + 1);
            }

            return response()->json([ 'slug' => $slug ]);

        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // return failed message
            return null;
        }
    }

}
