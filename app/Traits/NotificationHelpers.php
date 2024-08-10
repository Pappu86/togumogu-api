<?php

namespace App\Traits;

use App\Jobs\Notification\SendMobileMessage;
use App\Models\User\Customer;
use App\Jobs\Notification\SendPushNotification;
use App\Mail\OrderMail;
use App\Models\Order\Order;
use App\Models\Payment\PaymentMethod;
use App\Notifications\Community\CommentAddedIntoPost;
use App\Notifications\Community\ReactionAddedIntoComment;
use App\Notifications\Community\ReactionAddedIntoPost;
use App\Notifications\Community\ReplyAddedIntoComment;
use App\Notifications\Community\ReportedAddedIntoPost;
use App\Notifications\Community\ReportedIntoComment;
use App\Notifications\Order\OrderCreated;
use App\Notifications\Order\OrderStatusChanged;
use App\Notifications\Reward\ReferralReward;
use App\Traits\CommonHelpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

trait NotificationHelpers {
    
    /**
     * Notify to customer when comment added into a post
     */
    public static function SendNotifyOfCommentAddedIntoPost($receiver_ids, $customer, $comment, $post)
    {
       
        if(count($receiver_ids)>0) {
            $receivers = Customer::whereIn('id', $receiver_ids)->get();
            $commentHelpers = new CommonHelpers;

            foreach($receivers as $receiver){
                //Send database notifications 
                $receiver?->notify((new CommentAddedIntoPost($customer, $post, $comment)));                   
                
                //Send push notifications 
                if($commentHelpers?->isSettingEnabled($receiver, 'push_notification', 'all')){
                    if($commentHelpers?->isSettingEnabled($receiver, 'push_notification', 'comment')){
                        $fcm_tokens = DB::table('customer_devices')->where('customer_id', $receiver['id'])->pluck('token')->all();
                        $title = Lang::get('notification.someone_comment_the_post');
                        $body = Str::limit($comment?->content, 30);

                        $notification_data = collect([
                            "data"=> [
                                'type' => 'community_post',
                                'id' => $post?->id,
                                'comment_id' => $comment?->id,
                            ],
                            "title"=> $title,
                            "body"=> $body,
                            "fcm_tokens"=> $fcm_tokens,
                        ]);

                        SendPushNotification::dispatch($notification_data);
                    }
                }
            
            }
        }
        
    }

    /**
     * Notify to customer when reaction added into a post
     */
    public static function SendNotifyOfReactionAddedIntoPost($receiver_id, $customer, $post, $options)
    {
       $reaction_type = $options['type'];
       $commentHelpers = new CommonHelpers;

        if(isset($receiver_id)) {
            $receiver = Customer::where('id', $receiver_id)
                ->where('status', '=', 'active')
                ->first();

            //Send database notifications 
            $receiver?->notify(( new ReactionAddedIntoPost($customer, $post, $reaction_type)));                   
            
            //Send push notifications 
            if($commentHelpers?->isSettingEnabled($receiver, 'push_notification', 'all')){
                if($commentHelpers?->isSettingEnabled($receiver, 'push_notification', 'post_reaction',)){
                    $fcm_tokens = DB::table('customer_devices')->where('customer_id', $receiver['id'])->pluck('token')->all();
                    $title = Lang::get('notification.someone_'.$reaction_type.'_the_post');
                    $body = Str::limit($post?->content, 30);

                    $notification_data = collect([
                        "data"=> [
                            'type' => 'community_post',
                            'id' => $post?->id,
                        ],
                        "title"=> $title,
                        "body"=> $body,
                        "fcm_tokens"=> $fcm_tokens,
                    ]);

                    SendPushNotification::dispatch($notification_data);
                }
            }
        }
        
    }

    /**
     * Notify to customer when reply added into a comment
     */
    public static function SendNotifyOfReplyAddedIntoComment($receiver_ids, $customer, $comment, $post, $reply)
    {
       
        if(count($receiver_ids)>0) {
            $receivers = Customer::whereIn('id', $receiver_ids)->get();
            $commentHelpers = new CommonHelpers;

            foreach($receivers as $receiver){
                //Send database notifications 
                $receiver?->notify((new ReplyAddedIntoComment($customer, $post, $comment, $reply)));                   
                
                //Send push notifications 
                if($commentHelpers?->isSettingEnabled($receiver, 'push_notification', 'all')){
                    if($commentHelpers?->isSettingEnabled($receiver, 'push_notification', 'comment_reply')){
                        $fcm_tokens = DB::table('customer_devices')->where('customer_id', $receiver['id'])->pluck('token')->all();
                        $title = Lang::get('notification.someone_reply_the_comment');
                        $body = Str::limit($comment?->content, 30);
    
                        $notification_data = collect([
                            "data"=> [
                                'type' => 'community_post',
                                'id' => $post?->id,
                                'comment_id' => $comment?->id,
                            ],
                            "title"=> $title,
                            "body"=> $body,
                            "fcm_tokens"=> $fcm_tokens,
                        ]);
    
                        SendPushNotification::dispatch($notification_data);
                    }
                }
                
            }
        }
    }

    /**
     * Notify to customer when reaction added into a comment
     */
    public static function SendNotifyOfReactionAddedIntoComment($receiver_ids, $customer, $post, $comment, $options)
    {
       $reaction_type = $options['type'];

       if(count($receiver_ids)>0) {
        $receivers = Customer::whereIn('id', $receiver_ids)->where('status', '=', 'active')->get();
        $commentHelpers = new CommonHelpers;

            foreach($receivers as $receiver){
                //Send database notifications 
                $receiver?->notify((new ReactionAddedIntoComment($customer, $post, $comment, $reaction_type)));                   

                //Send push notifications 
                if($commentHelpers?->isSettingEnabled($receiver, 'push_notification', 'all')){
                    if($commentHelpers?->isSettingEnabled($receiver, 'push_notification', 'comment_reaction')){
                        $fcm_tokens = DB::table('customer_devices')->where('customer_id', $receiver['id'])->pluck('token')->all();
                        $title = Lang::get('notification.someone_'.$reaction_type.'_the_comment');
                        $body = Str::limit($post?->content, 30);
    
                        $notification_data = collect([
                            "data"=> [
                                'type' => 'community_post',
                                'id' => $post?->id,
                            ],
                            "title"=> $title,
                            "body"=> $body,
                            "fcm_tokens"=> $fcm_tokens,
                        ]);
    
                        SendPushNotification::dispatch($notification_data);  
                    }
                } 
            
            }
        }
    }

    /**
     * Notify to customer and admin when create and update order status
     * @return string
     * ection
     */
    public function sendNotifyOfOrderConfirm($order_id): string
    {

        $order_info = Order::with('customer', 'orderStatus', 'paymentStatus', 'products')
            ->where('id', '=', $order_id)->first();
        $customer = $order_info?->customer?:"";

        $order_no = $order_info->order_no;
        $delivery_email = $order_info->delivery_email?:null;
        $delivery_mobile = $order_info->delivery_mobile?:null;
        
        // send confirm message to customer sms/email
        $customer_email = $delivery_email??$customer?->email;
        $customer_mobile = $delivery_mobile??$customer?->mobile;
        $admin_email = config('helper.mail_to_order');

        $static_address = $order_info?->static_address?:'';
        $delivery_address = $static_address['shipping']?:'';
     
        $body = [];
        $body['order_no'] = $order_no;
        $body['invoice_no'] = $order_info?->invoice_no?:'-';

        $order_created_at = $order_info?->created_at?:'';
        if(isset($order_created_at)) {
           $order_parse_date = Carbon::parse($order_created_at);
            $body['order_date'] = $order_parse_date->isoFormat('D MMMM YYYY');
        }

        $payment_method_code = $order_info?->payment_method;
        $payment_method_info = PaymentMethod::with('translation')->where('code', '=', $payment_method_code)->get()->first();
        $body['order_payment_method'] = $payment_method_info?->name?:'-';
        $body['total_quantity'] = $order_info?->total_quantity?:0;
       
        $body['order_info'] = $order_info;
        $body['customer_name'] = $delivery_address['name']?:"-";
        $body['customer_contact_number'] = $delivery_address['mobile']?:'-';
        
        $address_line = $delivery_address['address_line']?:'';
        $area = $delivery_address['area']?:'';
        $district = $delivery_address['district']?:'';
        $division = $delivery_address['division']?:'';
        $zip = $delivery_address['zip']?:'';
        $zip_code = $zip?"-$zip":'';
        $body['order_delivery_address'] = "$address_line, $area, $district, $division, $zip_code";
        $body['payment_status'] = $order_info?->payment_status?:'-';
        
        $body['order_special_note'] = $order_info?->comment?:'-';

        $products = $order_info?->products;
        $body['products'] = $products;
        $body['shipping_cost'] = $order_info?->shipping_cost;
        $body['total_amount'] = $order_info?->total_amount;
        $body['coupon_discount'] = $order_info?->coupon_discount;
        $body['special_discount'] = $order_info?->special_discount;
        $sub_total_amount = 0;

        foreach ($products as $product) {
            $sub_total_amount += $product->quantity * $product->selling_unit_price;
          };

        $body['sub_total_amount'] = $sub_total_amount;

        //Send message/SMS to customer email
        if(isset($customer_email)) {
            $body['subject'] = "Your ToguMogu order invoice ". $order_no;
            $body['is_customer_mail'] = true;
            Mail::to($customer_email)->send(new OrderMail($body, null));
        }

        //Send message/SMS to customer mobile
        if($customer_mobile) {            
            $messageData = [ 
                'text' => "ToguMogu- We have received your order ". $order_info->order_no .". Please check your app for order status update. Thank you."
            ];
            SendMobileMessage::dispatch($customer_mobile, $messageData);
        }

        //Send order created confirmation notification
        $commentHelpers = new CommonHelpers;
  
        //Send database notifications 
        $customer?->notify((new OrderCreated($customer, $order_info))); 

        //Send push notifications 
        if($commentHelpers?->isSettingEnabled($customer, 'push_notification', 'all')){
            if($commentHelpers?->isSettingEnabled($customer, 'order', 'push_notification')){
                $FcmTokens = DB::table('customer_devices')
                    ->where('customer_id', '=', $customer?->id)
                    ->pluck('token')->all();

                $notification_data = collect([
                    "data"=> [
                        "type" => "order",
                        "id" => $order_info?->id
                    ],
                    "title"=> "We have received your order ". $order_info?->order_no ." Please check your app for order status update. Thank you.",
                    "body"=> "Please see details for your order status.",
                    "fcm_tokens"=> $FcmTokens,
                ]);
                SendPushNotification::dispatch($notification_data);
            }
        }

        //Order confirmation message to admin email
        if(isset($admin_email)) {
            $body['subject'] = "New Order $order_no";
            $body['is_customer_mail'] = false;
            $body['admin_email'] = $admin_email;
            Mail::to($admin_email)->send(new OrderMail($body, null));    
        }

        return 'success';
    }

    /**
     * Notify to customer when order update status
     * @param Order $order
     * @param Order $oldOrder
     * @return string
     */
    public function sendNotifyOfOrderStatusChanged($order, $oldOrder): string
    {

        $customer = $order?->customer?:"";
        $order_no = $order->order_no;
        $delivery_email = $order->delivery_email?:null;
        $delivery_mobile = $order->delivery_mobile?:null;
        
        // send confirm message to customer sms/email
        $customer_email = $delivery_email??$customer?->email;
        $customer_mobile = $delivery_mobile??$customer?->mobile;

        $static_address = $order?->static_address?:'';
        $delivery_address = $static_address['shipping']?:'';
     
        $body = [];
        $body['order_no'] = $order_no;
        $body['invoice_no'] = $order?->invoice_no?:'-';

        $order_created_at = $order?->created_at?:'';
        if(isset($order_created_at)) {
           $order_parse_date = Carbon::parse($order_created_at);
            $body['order_date'] = $order_parse_date->isoFormat('D MMMM YYYY');
        }

        $payment_method_code = $order?->payment_method;
        $payment_method_info = PaymentMethod::with('translation')->where('code', '=', $payment_method_code)->get()->first();
        $body['order_payment_method'] = $payment_method_info?->name?:'-';
        $body['total_quantity'] = $order?->total_quantity?:0;
       
        $body['order_info'] = $order;
        $body['customer_name'] = $delivery_address['name']?:"-";
        $body['customer_contact_number'] = $delivery_address['mobile']?:'-';
        
        $address_line = $delivery_address['address_line']?:'';
        $area = $delivery_address['area']?:'';
        $district = $delivery_address['district']?:'';
        $division = $delivery_address['division']?:'';
        $zip = $delivery_address['zip']?:'';
        $zip_code = $zip?"-$zip":'';
        $body['order_delivery_address'] = "$address_line, $area, $district, $division, $zip_code";
        $body['payment_status'] = $order?->payment_status?:'-';
        
        $body['order_special_note'] = $order?->comment?:'-';

        $products = $order?->products;
        $body['products'] = $products;
        $body['shipping_cost'] = $order?->shipping_cost;
        $body['total_amount'] = $order?->total_amount;
        $body['coupon_discount'] = $order?->coupon_discount;
        $body['special_discount'] = $order?->special_discount;
        $sub_total_amount = 0;

        foreach ($products as $product) {
            $sub_total_amount += $product->quantity * $product->selling_unit_price;
          };

        $body['sub_total_amount'] = $sub_total_amount;
        $order_status_name = $order?->orderStatus?->name;
        
        $serviceSettings = DB::table('settings')
            ->where('status', 'active')
            ->where('key', 'contact_phone')
            ->where('category', 'contact')
            ->first();

        $body['service_number'] = $serviceSettings?->value;
        $body['order_status'] = $order_status_name;
        //Send notification to customer email
        if(isset($customer_email)) {
            $body['subject'] = "Your order ". $order_no ." has been updated as ".$order_status_name;
            Mail::to($customer_email)->send(new OrderMail($body, 'status_changed'));
        }

        //Send message/SMS to customer mobile
        if($customer_mobile) {
            $messageData = [ 'text' => "Your order {$order->order_no} has been  updated as $order_status_name"];
            SendMobileMessage::dispatch($customer_mobile, $messageData);
        }

        //Send database notifications 
        $customer?->notify((new OrderStatusChanged($customer, $order, $oldOrder))); 

        //Send push notifications 
        $commentHelpers = new CommonHelpers;
  
        if($commentHelpers?->isSettingEnabled($customer, 'push_notification', 'all')){
            if($commentHelpers?->isSettingEnabled($customer, 'order', 'push_notification')){
                $FcmTokens = DB::table('customer_devices')
                    ->where('customer_id', '=', $customer?->id)
                    ->pluck('token')->all();

                $notification_data = collect([
                    "data"=> [
                        "type" => "order",
                        "id" => $order?->id
                    ],
                    "title"=> "Your order ". $order_no ." has been updated as ".$order_status_name,
                    "body"=> "Please see details for your order status.",
                    "fcm_tokens"=> $FcmTokens,
                ]);
                SendPushNotification::dispatch($notification_data);
            }
        }

        return 'success';
    }


    /**
     * Notify to sender customer when sign up new customer by referral url
     * @param Customer $sender
     * @param Customer $receiver
     * @param $rewardTransaction
     * @return string
     */
    public function sendNotifyOfReferralRewardPoint($sender, $receiver, $rewardTransaction): string
    {
        
        //Send database notifications 
        $sender?->notify((new ReferralReward($sender, $receiver, $rewardTransaction))); 

        //Send push notifications 
        $commentHelpers = new CommonHelpers;
  
        if($commentHelpers?->isSettingEnabled($sender, 'push_notification', 'all')){
            if($commentHelpers?->isSettingEnabled($sender, 'reward', 'push_notification')){
                $FcmTokens = DB::table('customer_devices')
                    ->where('customer_id', '=', $sender?->id)
                    ->pluck('token')->all();

                    $receiverName = $receiver?->name;
                    $point = $rewardTransaction?->credit;

                $notification_data = collect([
                    "data"=> [
                        "type" => "reword",
                        "id" => $sender?->id
                    ],
                    "title"=> "You have got $point reward points 🎁",
                    "body"=> "Your friend $receiverName joined ToguMogu and you have got $point reward points.",
                    "fcm_tokens"=> $FcmTokens,
                ]);
                SendPushNotification::dispatch($notification_data);
            }
        }

        return 'success';
    }

    /**
     * Notify to customer when report added into a post
     */
    public static function SendNotifyOfReportedIntoPost($receiver_id, $customer, $post, $options)
    {
        if(isset($receiver_id)) {
            $receiver = Customer::where('id', $receiver_id)
                ->where('status', '=', 'active')
                ->first();

            //Send database notifications 
            $receiver?->notify(new ReportedAddedIntoPost($customer, $post));                   
            
            //Send push notifications 
            $fcm_tokens = DB::table('customer_devices')->where('customer_id', $receiver['id'])->pluck('token')->all();
            $title = Lang::get('notification.someone_reported_your_post');
            $body = Str::limit($post?->content, 30);

            $notification_data = collect([
                "data"=> [
                    'type' => 'community_post',
                    'id' => $post?->id,
                ],
                "title"=> $title,
                "body"=> $body,
                "fcm_tokens"=> $fcm_tokens,
            ]);

            SendPushNotification::dispatch($notification_data);
        }
        
    }

    
    /**
     * Notify to customer when reaction added into a comment
     */
    public static function SendNotifyOfReportedIntoComment($receiver_ids, $customer, $post, $comment, $options)
    {
       $reaction_type = $options['type'];

       if(count($receiver_ids)>0) {
        $receivers = Customer::whereIn('id', $receiver_ids)->where('status', '=', 'active')->get();
        $commentHelpers = new CommonHelpers;

            foreach($receivers as $receiver){
                //Send database notifications 
                $receiver?->notify((new ReportedIntoComment($customer, $post, $comment, $reaction_type)));                   

                //Send push notifications 
                $fcm_tokens = DB::table('customer_devices')->where('customer_id', $receiver['id'])->pluck('token')->all();
                $title = Lang::get('notification.someone_reported_your_comment');
                $body = Str::limit($post?->content, 30);

                $notification_data = collect([
                    "data"=> [
                        'type' => 'community_post',
                        'id' => $post?->id,
                    ],
                    "title"=> $title,
                    "body"=> $body,
                    "fcm_tokens"=> $fcm_tokens,
                ]);

                SendPushNotification::dispatch($notification_data);  
            }
        }
    }
}