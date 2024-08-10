<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Http\Resources\Community\PostAppResource;
use App\Http\Resources\Community\VoteAppResource;
use App\Models\Common\Hashtag;
use App\Models\Community\Favourite;
use App\Models\Community\Post;
use App\Models\Community\Topic;
use App\Models\Community\Vote;
use App\Traits\CommunityHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use App\Traits\NotificationHelpers;
use Throwable;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class PostAppController extends Controller
{

    use NotificationHelpers, CommunityHelpers;

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function getPosts(Request $request): AnonymousResourceCollection
    {
        $customer_id = Auth::id();
        $limit = (int)$request->query('limit', 10);
        $page = (int)$request->query('page', 1);
        $age_group_id = $request->query('age_group_id');
        $topic_slug = $request->query('topic_slug');

        $posts = Post::with(['comments', 'customer', 'ageGroup', 'likes',
         'dislikes', 'hashtags', 'topics'])
            ->latest()
            ->where('status', '=', 'active')
            ->where('visible', '=', '1')
            ->orWhere(function($query) use ($customer_id) {
                $query->where('customer_id', '=', $customer_id)
                    ->where('status', '=', 'active')
                    ->where('visible', '=', '0');
            });

        $posts = collect($posts->get());
        //Filtering by age group id
        if(isset($age_group_id)) {
            $posts = $posts->where('age_group_id', '=', $age_group_id);
        }

        //Filtering by topic id
        if(isset($topic_slug)) { 
            $topic_id = Topic::with('translations')
                ->whereTranslation('slug', $topic_slug)
                ->first();

            $ancestors = Topic::with('translations')->ancestorsAndSelf($topic_id)->pluck('id');
            $descendants = Topic::with('translations')->descendantsAndSelf($topic_id)->pluck('id');

            // get topic ids
            $topics_ids = collect($ancestors)->merge($descendants)->unique()->values()->toArray();
            $post_ids = DB::table('post_topic_post')
                ->whereIn('topic_id', $topics_ids)
                ->pluck('post_id')
                ->toArray();
            $posts = $posts->whereIn('id', $post_ids);
        }

        $total = $posts->count();
        $starting_point = ($page * $limit) - $limit;
        $posts = array_slice($posts->all(), $starting_point, $limit, true);
        
        $paginator = new Paginator($posts, $total, $limit, $page, [
            'path'  => $request->url(),
            'query' => $request->query(),
        ]);

        return PostAppResource::collection($paginator);
    }

    /**
     * Create the specified resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'content' => 'required',
        ]);

        $customer_id = Auth::id();

        // begin database transaction
        DB::beginTransaction();
        try {

            $content = Str::substr($request->get('content'), 0, 30);
            $slug = $this->checkSlug($content);

            $request->merge([
                'customer_id' => $customer_id,
                'status' => 'active',
                'slug' => $slug,
                'visible' => '0',
            ]);
            $post = Post::create($request->all());

            // // create images
            if ($request->has('images')) {

                $image = $request->file('images');
                $url = $post->addMedia($image)->toMediaCollection('posts')->getFullUrl();
                $images = array([
                    "src" => $url,
                    "srcset" => null,
                    "lazy" => null,
                    "is_featured" => false
                ]);

                //     $images_array = $request->file('images');
                //     $images = array();
                //     Log::info($images_array);

                //    if(isset($images_array)) {
                //     foreach($images_array as $image)
                //         {
                //             $url = $post->addMedia($image)->toMediaCollection('posts')->getFullUrl();

                //             Log::info("adfasd". $url);
                //             array_push($images, [
                //                 "src" => $url,
                //                 "srcset" => null,
                //                 "lazy" => null,
                //                 "is_featured" => false
                //                 ]);   
                //         }
                //    }

                $request->merge([ 'images' => $images ]);
                $post->images()->createMany($request->get('images'));
            }

            // create topics of the post
            if ($request->filled('topics')) {
                $items = collect($request->input('topics'))->pluck('id');
                $post->topics()->sync($items);
            }

            // create hashtags of the post
            if ($request->filled('hashtags')) {
                $hashtags = $request->get('hashtags');
                if (count($hashtags) > 0) {
                    $new_hashtags = [];

                    foreach ($hashtags as $hashtag) {
                        $exist_hashtag = Hashtag::where('name', '=', $hashtag)->first();
                        if (isset($exist_hashtag) && $exist_hashtag->id) {
                            array_push($new_hashtags, $exist_hashtag->id);
                        } else {
                            $new_hashtag = Hashtag::create(["name" => $hashtag]);
                            array_push($new_hashtags, $new_hashtag->id);
                        }
                    }
                    $post->hashtags()->sync($new_hashtags);
                }
            }

            // commit database
            DB::commit();

           $rewardPoints = 0;
           if(isset($post)) {
            //   $rewardPoints = $this->addPostCreatedRewardPoints($post);
           }

            // return success message
            return response()->json([
                'message' => Lang::get('crud.create'),
                'data' => new PostAppResource($post),
                'rewardPoints' => $rewardPoints
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
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, Post $post): JsonResponse
    {

        if ($request->has('content')) {
            $this->validate($request, [
                'content' => 'required',
            ]);
        }

        $customer_id = Auth::id();
        if ($post?->customer_id !== $customer_id) {
            return response()->json(['message' => Lang::get('auth.not_allow')], 401);
        }

        // begin database transaction
        DB::beginTransaction();
        try {
            $post->update($request->all());

            // Update post topics
            if ($request->filled('topics')) {
                $items = collect($request->input('topics'))->pluck('id');
                $post->topics()->sync($items);
            }

            // create hashtags of the post
            if ($request->filled('hashtags')) {
                $hashtags = $request->get('hashtags');
                $new_hashtags = [];

                if (count($hashtags) > 0) {
                    foreach ($hashtags as $hashtag) {
                        $exist_hashtag = Hashtag::where('name', '=', $hashtag)->first();
                        if (isset($exist_hashtag) && $exist_hashtag->id) {
                            array_push($new_hashtags, $exist_hashtag->id);
                        } else {
                            $new_hashtag = Hashtag::create(["name" => $hashtag]);
                            array_push($new_hashtags, $new_hashtag->id);
                        }
                    }
                }

                $post->hashtags()->sync($new_hashtags);
            }

            // commit database
            DB::commit();

            // return success message
            return response()->json([
                'message' => Lang::get('crud.update'),
                'data' => new PostAppResource($post)
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
     * @param Post $post
     * @return PostAppResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show(Post $post): PostAppResource|JsonResponse
    {

        try {
            return new PostAppResource($post);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
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

        $customer_id = Auth::id();
        if ($post?->customer_id !== $customer_id) {
            return response()->json(['message' => Lang::get('auth.not_allow')], 401);
        }

        // begin database transaction
        DB::beginTransaction();
        try {
            // delete post
            $post->delete();

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.trash')
            ]);
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
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function updateImages(Request $request, Post $post): JsonResponse
    {

        $customer_id = Auth::id();
        if ($post?->customer_id !== $customer_id) {
            return response()->json(['message' => Lang::get('auth.not_allow')], 401);
        }

        // begin database transaction
        DB::beginTransaction();
        try {

            // delete existing images
            $post->images()->delete();

            if ($request->has('images')) {
                $image = $request->file('images');
                $url = $post->addMedia($image)->toMediaCollection('posts')->getFullUrl();
                $images = array([
                    "src" => $url,
                    "srcset" => null,
                    "lazy" => null,
                    "is_featured" => false
                ]);

                $request->merge([ 'images' => $images ]);
                $post->images()->createMany($request->get('images'));
            }

            // commit database
            DB::commit();

            // return success message
            return response()->json([
                'message' => Lang::get('crud.update'),
                'data' => new PostAppResource($post)
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
     * @param $content
     * @return string
     */
    private function checkSlug($content)
    {
        try {
            $slug = Str::slug($content, '-');

            # slug repeat check
            $latest = DB::table('posts')->where('slug', '=', $slug)
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
     * Create the specified resource in storage.
     *
     * @param Post $post
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function like(Post $post): JsonResponse
    {

        $customer = Auth::user();
        $post_id = $post?->id;
        $customer_id = $customer?->id;

        if (!isset($post_id)) {
            return response()->json(['message' => "Please, select a post!"], 401);
        }

        // begin database transaction
        DB::beginTransaction();
        try {

            $vote = Vote::where('customer_id', '=', $customer_id)
                ->where('post_id', '=', $post_id);

            if ($vote?->count() > 0) {
                $is_like = $vote->first()?->like;
                $vote->update([
                    'like' => $is_like ? 0 : 1,
                    'dislike' => 0
                ]);

                $is_like_notify = $is_like ? false : true;

            } else {
                $vote = Vote::create([
                    'status' => 'active',
                    'customer_id' => $customer_id,
                    'post_id' => $post_id,
                    'like' => 1,
                ]);

                $is_like_notify = true;
            }

            //Start Send notifications push and normal
            $receiver_id = $post?->customer_id;

            if( $receiver_id !== $customer_id && $is_like_notify) {
                $options = [ 'type' => 'like' ];
                $this->SendNotifyOfReactionAddedIntoPost($receiver_id, $customer, $post, $options);
            }
            //End Send notifications push and normal

            // commit database
            DB::commit();

            // return success message
            return response()->json([
                'message' => Lang::get('crud.update'),
                'data' => new VoteAppResource($vote->first())
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
     * Create the specified resource in storage.
     *
     * @param Post $post
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function dislike(Post $post): JsonResponse
    {

        $customer = Auth::user();
        $post_id = $post?->id;
        $customer_id = $customer?->id;

        if (!isset($post_id)) {
            return response()->json(['message' => "Please, select a post!"], 401);
        }

        // begin database transaction
        DB::beginTransaction();
        try {

            $vote = Vote::where('customer_id', '=', $customer_id)
                ->where('post_id', '=', $post_id);

            if ($vote?->count() > 0) {
                $is_dislike = $vote->first()?->dislike;
                $vote->update([
                    'dislike' => $is_dislike ? 0 : 1,
                    'like' => 0,
                ]);
            } else {
                $vote = Vote::create([
                    'status' => 'active',
                    'customer_id' => $customer_id,
                    'post_id' => $post_id,
                    'dislike' => 1,
                ]);
            }

            // commit database
            DB::commit();

            // return success message
            return response()->json([
                'message' => Lang::get('crud.update'),
                'data' => new VoteAppResource($vote->first())
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
     * Create the specified resource in storage.
     *
     * @param Post $post
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function fevorite(Post $post): JsonResponse
    {

        $customer = Auth::user();
        $post_id = $post?->id;
        $customer_id = $customer?->id;

        if (!isset($post_id)) {
            return response()->json(['message' => "Please, select a post!"], 401);
        }
        if (!isset($customer_id)) {
            return response()->json(['message' => Lang::get('auth.not_allow')], 401);
        }

        // begin database transaction
        DB::beginTransaction();
        try {

            $favourite = Favourite::where('customer_id', '=', $customer_id)
                ->where('post_id', '=', $post_id);

            if ($favourite?->count() > 0) {
                $isFavorite = !$favourite->delete();
            } else {
                Favourite::create(['customer_id' => $customer_id, 'post_id' => $post_id]);
                $isFavorite = !!Favourite::where('customer_id', '=', $customer_id)->where('post_id', '=', $post_id)->count();
            }

            // commit database
            DB::commit();

            // return success message
            return response()->json([
                'message' => Lang::get('crud.update'),
                'isFavorite' => $isFavorite
            ]);
        } catch (Throwable $exception) {
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
     * Create the specified post report.
     *
     * @param Post $post
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function reportStore(Post $post): JsonResponse
    {

        $customer = Auth::user();
        $post_id = $post?->id;
        $customer_id = $customer?->id;

        if (!isset($post_id)) {
            return response()->json(['message' => "Please, select a post!"], 401);
        }

        if (!isset($customer_id)) {
            return response()->json(['message' => Lang::get('auth.not_allow')], 401);
        }

        // begin database transaction
        DB::beginTransaction();
        try {

            // return success message
            return response()->json([
                'message' => "Your report has been submitted to our moderators. Please wait sometime to see the result!",
            ]);
            
        } catch (Throwable $exception) {
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
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     * @return AnonymousResourceCollection
     */
    public function getAllFevorite(Request $request): AnonymousResourceCollection
    {
        $customer = Auth::user();
        $customer_id = $customer?->id;
        if (!isset($customer_id)) {
            return response()->json(['message' => Lang::get('auth.not_allow')], 401);
        }

        $post_ids = Favourite::Latest()
            ->where('customer_id', '=', $customer_id)
            ->pluck('post_id');

        $posts = Post::latest()->whereIn('id', $post_ids);
        $posts = $posts->cursorPaginate(10);

        return PostAppResource::collection($posts);
    }

    function removeFromArr($arr, $val)
    {
        unset($arr[array_search($val, $arr)]);
        return array_values($arr);
    }
}
