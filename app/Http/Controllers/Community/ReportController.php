<?php

namespace App\Http\Controllers\Community;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Community\PostEditResource;
use App\Http\Resources\Community\ReportResource;
use App\Models\Community\Post;
use App\Models\Community\Report as CommunityReport;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Throwable;

class ReportController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request): AnonymousResourceCollection
    {

        $this->authorize('viewAny report');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $reports = CommunityReport::query()->latest();
        
        if ($query) {
            $reports = $reports->whereLike('note', '%' . $query . '%');
        }
        
        if ($sortBy) {
            $reports = $reports->orderBy($sortBy, $direction);
        }
        
        if ($per_page === '-1') {
            $results = $reports->get();
            $reports = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $reports = $reports->paginate($per_page);
        }

        return ReportResource::collection($reports);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(Request $request): JsonResponse
    {

        $customer_id = Auth::id();
        $post_id = $request->get('post_id');
        $comment_id = $request->get('comment_id');

        if(!$customer_id ) {
            return response()->json([ 'message' => Lang::get('auth.not_allow') ], 401);
        };

        // Check existing report was created by the customer and this post/comment
        $exist_report = CommunityReport::where('customer_id', $customer_id)
            ->get()
            ->filter(function ($report) use ($post_id, $comment_id) {
    
                if(isset($post_id) && $post_id === $report->post_id) {
                    return $report;
                }

                if(isset($comment_id) && $comment_id === $report->comment_id) {
                    return $report;
                }
            });

        if(count($exist_report)>0) {
            return response()->json([ 'message' => Lang::get('auth.already_created') ], 201);
        }

        // begin database transaction
        DB::beginTransaction();
        try {
           if(isset($customer_id)){
            $request->merge(['customer_id' => $customer_id]);
           }

            $report = CommunityReport::create($request->all());

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.sent'),
                'reportId' => $report?->id
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
     * @param Post $post
     * @return PostEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show(Post $post): PostEditResource|JsonResponse
    {

        $this->authorize('view post');

        try {
            return new PostEditResource($post);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * Edit community Report.
     *
     * @param CommunityReport $communityReport
     * @return PostEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function edit(Post $post): PostEditResource|JsonResponse
    {
        $this->authorize('update report');

        try {
            return new PostEditResource($post);
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
     * @param CommunityReport $report
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, CommunityReport $report): JsonResponse
    {

        $this->authorize('update report');

        // begin database transaction
        DB::beginTransaction();
        try {
            $report->update($request->all());

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
     * @param Post $post
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('delete post');

        // begin database transaction
        DB::beginTransaction();
        try {
            $post->delete();

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
