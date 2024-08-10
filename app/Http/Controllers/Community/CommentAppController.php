<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Http\Resources\Community\CommentAppResource;
use App\Http\Resources\Community\VoteAppResource;
use App\Models\Community\Comment;
use App\Models\Community\Post;
use App\Models\Community\Vote;
use App\Traits\CommunityHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use App\Traits\NotificationHelpers;
use Throwable;

class CommentAppController extends Controller
{

    use NotificationHelpers, CommunityHelpers;

    /**
     * @param Request $request
     * @param Post $post
     * @return AnonymousResourceCollection
     */
    public function getComments(Request $request, Post $post): AnonymousResourceCollection
    {
        $limit = (int)$request->query('limit', 10);

        $comments = $post->comments()
            ->oldest()
            ->where('status', '=', 'active')
            ->paginate($limit);

        return CommentAppResource::collection($comments);
    }

    /**
     * @param Request $request
     * @param Comment $comment
     * @return AnonymousResourceCollection
     */
    public function getReplies(Request $request, Comment $comment): AnonymousResourceCollection
    {
        $limit = (int)$request->query('limit', 10);

        $comments = $comment->replies()
            ->oldest()
            ->where('status', '=', 'active')
            ->paginate($limit);

        return CommentAppResource::collection($comments);
    }

    /**
     * Create the specified resource in storage.
     *
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(Request $request, Post $post): JsonResponse
    {

        $this->validate($request, [
            'content' => 'required',
        ]);

        $customer = Auth::user();

        // begin database transaction
        DB::beginTransaction();
        try {
          
            $comment = new Comment;
            $comment->content = $request->get('content');
            $comment->customer()->associate($customer);
            $post->comments()->save($comment);
    
            // commit database
            DB::commit();

            //Start Send notifications push and normal            
            $post_owner_id = $post?->customer_id;
            
            if($customer?->id !== $post_owner_id) {
                $this->SendNotifyOfCommentAddedIntoPost(array($post_owner_id), $customer, $comment, $post);
            }
            //End Send notifications push and normal

           //Start Adding reward point 
           $rewardPoints = 0;
           if(isset($post) && isset($comment)) {
              $rewardPoints = $this->addCommentOnPostRewardPoints($post, $comment);
           }
           //End Adding reward point 

            // return success message
            return response()->json([
                'message' => Lang::get('crud.create'),
                'data' => new CommentAppResource($comment),
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
     * Create the specified resource in storage.
     *
     * @param Request $request
     * @param Post $post
     * @param Comment $comment
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function reply(Request $request, Post $post, Comment $comment): JsonResponse
    {

        $this->validate($request, [
            'content' => 'required',
        ]);

        $customer = Auth::user();

        // begin database transaction
        DB::beginTransaction();
        try {
          
            $reply = new Comment();
            $reply->content = $request->get('content');
            $reply->customer()->associate($customer);
            $reply->parent_id = $comment?->id;
            $post->comments()->save($reply);
    
            //Start Send notifications push and normal
            $post = $comment?->post;
            $comment_owner_id = $comment?->customer_id;
            
            if($customer?->id !== $comment_owner_id) {
                $this->SendNotifyOfReplyAddedIntoComment(array($comment_owner_id), $customer, $comment, $post, $reply);
            }
            //End Send notifications push and normal

            // commit database
            DB::commit();

            // return success message
            return response()->json([
                'message' => Lang::get('crud.create'),
                'data' => new CommentAppResource($reply)
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
     * @param Comment $comment
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, Comment $comment): JsonResponse
    { 
        $this->validate($request, [
            'content' => 'required',
        ]);

        $customer_id = Auth::id();
        if($comment?->customer_id !== $customer_id ) {
            return response()->json([ 'message' => Lang::get('auth.not_allow') ], 401);
        }

        // begin database transaction
        DB::beginTransaction();
        try {

            $comment->update($request->all());
            $comment = Comment::where('id', '=', $comment?->id)->first();
            
            // commit database
            DB::commit();

            // return success message
            return response()->json([
                'message' => Lang::get('crud.update'),
                'data' => new CommentAppResource($comment)
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
        $customer_id = Auth::id();
        if($comment?->customer_id !== $customer_id ) {
            return response()->json([ 'message' => Lang::get('auth.not_allow') ], 401);
        }

        // begin database transaction
        DB::beginTransaction();
        try {
            // delete post
            $comment->delete();

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
     * Create the specified resource in storage.
     *
     * @param Request $request
     * @param Comment $comment
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function like(Request $request, Comment $comment): JsonResponse
    {

        $customer = Auth::user();
        $comment_id = $comment?->id;
        $customer_id = $customer?->id;
        
        if(!isset($comment_id)){ return response()->json([ 'message' => "Please, select a post!"], 401); }

        // begin database transaction
        DB::beginTransaction();
        try {
        
            $vote = Vote::where('customer_id', '=', $customer_id)
                            ->where('comment_id', '=', $comment_id);

            if($vote?->count()>0) {
                $is_like = $vote->first()?->like;
                $vote->update([
                     'like' => $is_like?0:1,
                     'dislike' => 0,
                    ]);

                $is_like_notify = $is_like ? false : true;

            } else {
               $vote = Vote::create([
                    'status' => 'active',
                    'customer_id' => $customer_id,
                    'comment_id' => $comment_id,
                    'like' => 1,
                ]);
                
                $is_like_notify = true;
            }              

            // commit database
            DB::commit();

            //Start Send notifications push and normal
            if($is_like_notify) {  
                $post = $comment?->post;
                $comment_owner_id = $comment?->customer_id;
                
                if($customer?->id !== $comment_owner_id) {
                    $options = [ 'type' => 'like' ];
                    $this->SendNotifyOfReactionAddedIntoComment(array($comment_owner_id), $customer, $post, $comment, $options);        
                }
            }
           //End Send notifications push and normal

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
     * @param Request $request
     * @param Comment $comment
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function dislike(Request $request, Comment $comment): JsonResponse
    {

        $customer = Auth::user();
        $comment_id = $comment?->id;
        $customer_id = $customer?->id;
        
        if(!isset($comment_id)){ return response()->json([ 'message' => "Please, select a post!"], 401); }

        // begin database transaction
        DB::beginTransaction();
        try {
        
            $vote = Vote::where('customer_id', '=', $customer_id)
                            ->where('comment_id', '=', $comment_id);

            if($vote?->count()>0) {
                $is_dislike = $vote->first()?->dislike;
                $vote->update([
                     'dislike' => $is_dislike?0:1,
                     'like' => 0,
                    ]);
            } else {
               $vote = Vote::create([
                    'status' => 'active',
                    'customer_id' => $customer_id,
                    'comment_id' => $comment_id,
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

    function removeFromArr($arr, $val)
    {
        unset($arr[array_search($val, $arr)]);
        return array_values($arr);
    }
}
