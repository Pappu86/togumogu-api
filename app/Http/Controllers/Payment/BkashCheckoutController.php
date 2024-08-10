<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use URL;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Order\Order;
use Carbon\Carbon;

class BkashCheckoutController extends Controller
{  
    private $base_url;

    public function __construct()
    {
        // Sandbox 'https://tokenized.sandbox.bka.sh/v1.2.0-beta'
        // $this->base_url = 'https://tokenized.pay.bka.sh/v1.2.0-beta';
         $this->base_url = config('helper.bkash_checkout_base_url');               
    }

    public function authHeaders(){
        return array(
            'Content-Type:application/json',
            'Authorization:' .$this->grant(),
             'X-APP-Key:'.config('helper.bkash_checkout_app_key')
        );
    }
         
    public function curlWithBody($url,$header,$method,$body_data_json){
        $curl = curl_init($this->base_url.$url);
        curl_setopt($curl,CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_POSTFIELDS, $body_data_json);
        curl_setopt($curl,CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function grant()
    {
        $header = array(
                'Content-Type:application/json',
                'username:'.config('helper.bkash_checkout_username'),
                'password:'.config('helper.bkash_checkout_password')
                );      
        $header_data_json=json_encode($header);
        $body_data = array('app_key'=> config('helper.bkash_checkout_app_key'), 'app_secret'=>config('helper.bkash_checkout_app_secret'));
        $body_data_json=json_encode($body_data);
    
        $response = $this->curlWithBody('/tokenized/checkout/token/grant',$header,'POST',$body_data_json);
        $token = json_decode($response)->id_token;
        return $token;
    }

    public function create($request)
    {
        $header =$this->authHeaders();
        $totalAmount=$request['amount'] ? $request['amount'] : 0;
        $orderId=$request['orderId'] ? $request['orderId']:'';
        $orderNo=$request['order_no'] ? $request['order_no']:'';
        $website_url = URL::to("/");
        $body_data = array(
            'mode' => '0011',
            'payerReference' => ' ',
            'callbackURL' => $website_url.'/api/bkash/callback/?orderId='.$request['orderId'],
            'amount' => $request['amount'] ? $request['amount'] : 0,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $orderNo,
        );
        $body_data_json=json_encode($body_data);
        $response = $this->curlWithBody('/tokenized/checkout/create',$header,'POST',$body_data_json);
        return $response;
    }

    public function execute($paymentID)
    {

        $header =$this->authHeaders();

        $body_data = array(
            'paymentID' => $paymentID
        );
        $body_data_json=json_encode($body_data);

        $response = $this->curlWithBody('/tokenized/checkout/execute',$header,'POST',$body_data_json);

        $res_array = json_decode($response,true);
        return $response;
    }

    public function query($paymentID)
    {

        $header =$this->authHeaders();

        $body_data = array(
            'paymentID' => $paymentID,
        );
        $body_data_json=json_encode($body_data);

        $response = $this->curlWithBody('/tokenized/checkout/payment/status',$header,'POST',$body_data_json);
        
        $res_array = json_decode($response,true);

         return $response;
    }

    public function callback(Request $request)
    {
        $allRequest = $request->all();
        $orderId = $allRequest['orderId'];

        #Check order status in order table against the transaction id or order id.
        $order_details = DB::table('orders')
            ->where('id', '=', $orderId)
            ->select('id', 'order_no', 'payment_status', 'order_status', 'total_amount', 'platform')
            ->first();
        
        if(isset($allRequest['status']) && $allRequest['status'] == 'success'){                
            $response = $this->execute($allRequest['paymentID']);
            $arr = json_decode($response,true);
            
            if(array_key_exists("message",$arr)){
                // if execute api failed to response
                sleep(1);
                $response = $this->query($allRequest['paymentID']);
                $arr = json_decode($response,true);
            }            

            if(array_key_exists("statusCode",$arr) && $arr['statusCode'] != '0000'){
                $order = Order::query()->where('id', '=', $orderId)->first();
                if(isset($order)) {
                    $order->update([
                            'order_status' => 'pending',
                            'payment_status' => 'failed',
                        ]);
                }
                // redirect to frontend                
                $url = config('helper.url') . '/checkout/fail/?data='.$arr['statusMessage'];
                // check for app
                if ($order->platform !== 'web') {
                    $url = 'https://togumogu.page.link/order-fail';
                }
                return redirect($url);
            }else{
                // response save to your db
                if($orderId){                    
                    if (isset($order_details) && $order_details->payment_status === 'pending') {
                        $order = Order::query()->where('id', '=', $orderId)->first();
                        if(isset($order)) {
                            $order->update([
                                'order_status' => 'processing',
                                'payment_status' => 'paid',
                            ]);
                            // insert payment referance
                            DB::table('payment_referances')->insert([
                                'order_id' => $orderId,
                                'status' => 'active',
                                'ref_number' => $allRequest['paymentID'],
                                'payment_method_code' => $order->payment_method,
                                'customer_id' => $order->customer_id,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ]);
                        }                        
                    }
                }
                // redirect to frontend
                $url = config('helper.url') . '/user/orders/' . $orderId;
                // check for app
                if ($order_details->platform !== 'web') {
                    $url = 'https://togumogu.page.link/order-success/' . $orderId;
                }
                return redirect($url);
            }

        }else{
             $url = config('helper.url') . '/checkout/fail/?data='.$allRequest['status'];
            // check for app
            if ($order_details->platform !== 'web') {
                $url = 'https://togumogu.page.link/order-cancel';
            }
            return redirect($url);
        }

    }

    public function refund(Request $request)
    {
        $header =$this->authHeaders();

        $body_data = array(
            'paymentID' => $request->paymentID,
            'amount' => $request->amount,
            'trxID' => $request->trxID,
            'sku' => 'sku',
            'reason' => 'Quality issue'
        );
     
        $body_data_json=json_encode($body_data);

        $response = $this->curlWithBody('/tokenized/checkout/payment/refund',$header,'POST',$body_data_json);
        
        // your database operation
        // save $response
        
        return $response;
    } 
}
