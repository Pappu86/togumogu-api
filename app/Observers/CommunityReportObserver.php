<?php

namespace App\Observers;

use App\Models\Community\Comment;
use App\Models\Community\Post;
use App\Models\Community\Report as CommunityReport;
use App\Traits\NotificationHelpers;
use Illuminate\Support\Facades\Auth;

class CommunityReportObserver
{

    use NotificationHelpers;

    /**
     * Handle the CommunityReport "created" event.
     *
     * @param CommunityReport $report
     * @return void
     */
    public function created(CommunityReport $report)
    {
        $category = $report?->category?:null;
        $reportedId = $report?->reported_id?:null;

        if(isset($category) && $category === 'post' && $reportedId) {
            Post::where('id', $reportedId)->update(['status'=>'inactive']);
        }
        
        if(isset($category) && $category === 'comment' && $reportedId ) {
            Comment::where('id', $reportedId)->update(['status'=>'inactive']);
        }
    }

    /**
     * Handle the CommunityReport "updated" event.
     *
     * @param  CommunityReport $report
     * @return void
     */
    public function updated(CommunityReport $report)
    {

        $status = $report?->status?:null;
        $category = $report?->category?:null;
        $reportedId = $report?->reported_id?:null;

        if(isset($category) && $category === 'post' && $reportedId) {
            $post = Post::where('id', $reportedId);
         }
         
         if(isset($category) && $category === 'comment' && $reportedId) {
            $comment = Comment::where('id', $reportedId);
         }

        if(isset($status) && $status ==='rejected') {
            if(isset($category) && $category === 'post') {
               $post->update(['status'=>'active']);
            }
            
            if(isset($category) && $category === 'comment') {
                $comment->update(['status'=>'active']);
            }
        }

        if(isset($status) && $status ==='approved') {
            if(isset($category) && $category === 'post') {
                //Start Send notifications push and normal
                $post = $post->first();
                $receiver_id = $post->customer_id;

                if( $receiver_id !== $report->customer_id) {
                    $customer = Auth::user();
                    $options = null;
                    $this->SendNotifyOfReportedIntoPost($receiver_id, $customer, $post, $options);
                }
                //End Send notifications push and normal
            }

            if(isset($category) && $category === 'comment') {
                //Start Send notifications push and normal
                $post = $comment?->post;
                $comment_owner_id = $comment?->customer_id;
                
                if($report->customer_id !== $comment_owner_id) {
                    $customer = Auth::user();
                    $options = null;
                    $this->SendNotifyOfReportedIntoComment(array($comment_owner_id), $customer, $post, $comment, $options);        
                }
                //End Send notifications push and normal
            }
        }
    }

}
