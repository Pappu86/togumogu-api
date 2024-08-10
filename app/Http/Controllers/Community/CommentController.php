<?php

namespace App\Http\Controllers\Community;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Community\CommentEditResource;
use App\Http\Resources\Community\CommentSingleResource;
use App\Http\Resources\Community\CommentResource;
use App\Models\Community\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Throwable;

class CommentController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request): AnonymousResourceCollection
    {

        $this->authorize('viewAny comment');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $comments = Comment::query()->latest();
        if ($query) {
            $comments = $comments->whereLike('content', '%' . $query . '%');
        }
        if ($sortBy) {
            $comments = $comments->orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $comments->get();
            $comments = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $comments = $comments->paginate($per_page);
        }
        return CommentResource::collection($comments);
    }

    /**
     * @param Comment $comment
     * @return CommentSingleResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show(Comment $comment): CommentSingleResource|JsonResponse
    {

        $this->authorize('view comment');

        try {
            return new CommentSingleResource($comment);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * Edit comment.
     *
     * @param Comment $comment
     * @return CommentEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function edit(Comment $comment): CommentEditResource|JsonResponse
    {
        $this->authorize('update comment');

        try {
            return new CommentEditResource($comment);
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
     * @param Comment $comment
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {

        $this->authorize('update comment');

        // begin database transaction
        DB::beginTransaction();
        try {

            $comment->update($request->all());

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
     * @param Comment $comment
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete comment');

        // begin database transaction
        DB::beginTransaction();
        try {
            $comment->delete();

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
