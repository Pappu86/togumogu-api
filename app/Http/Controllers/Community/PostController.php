<?php

namespace App\Http\Controllers\Community;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Community\PostEditResource;
use App\Http\Resources\Community\PostResource;
use App\Http\Resources\Community\PostSingleResource;
use App\Models\Community\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Throwable;

class PostController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request): AnonymousResourceCollection
    {

        $this->authorize('viewAny post');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $posts = Post::query()->latest();
        if ($query) {
            $posts = $posts->whereLike('name', '%' . $query . '%');
        }
        if ($sortBy) {
            $posts = Post::query()->orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $posts->get();
            $posts = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $posts = $posts->paginate($per_page);
        }
        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(): JsonResponse
    {
        $this->authorize('create post');

        // begin database transaction
        DB::beginTransaction();
        try {
            $post = Post::query()->create();


            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.create'),
                'postId' => $post->id
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
     * @return PostSingleResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show(Post $post): PostSingleResource|JsonResponse
    {

        $this->authorize('view post');

        try {
            return new PostSingleResource($post);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * Edit post.
     *
     * @param Post $post
     * @return PostEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function edit(Post $post): PostEditResource|JsonResponse
    {
        $this->authorize('update post');

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
     * @param Post $post
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, Post $post): JsonResponse
    {

        $this->authorize('update post');

        // begin database transaction
        DB::beginTransaction();
        try {

            $request->merge([
                'is_anonymous' => $request->get('is_anonymous')?1:0
            ]);

            $post->update($request->all());

            // Update post topics
            if ($request->filled('topics')) {
                $items = collect($request->input('topics'))->pluck('id');
                $post->topics()->sync($items);
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

    /**
     * @param $content
     * @return string
     */
    private function checkSlug($content)
    {
        try {
            $slug = Str::slug($content, '-');
            # slug repeat check
            $latest = Post::where('slug', '=', $slug)
                ->latest('id')
                ->value('slug');

            if ($latest) {
                $pieces = explode('-', $latest);
                $number = intval(end($pieces));
                $slug .= '-' . ($number + rand(0, 99999));
            }
            return $slug;
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // return failed message
            return null;
        }
    }


    /**
     * @return JsonResponse
     */
    public function generateDynamicLinks(): JsonResponse
    {
        Post::latest()
            ->where('status', '=', 'active')
            ->get()
            ->map(function ($post) {
                // generate dynamic link
                //AddDayCareDeepLink::dispatch($post);
            });

        return response()->json('Generated Successfully');
    }

}
