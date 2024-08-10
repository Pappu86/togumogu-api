<?php

namespace App\Observers;

use App\Models\Order\Order;
use App\Traits\CommonHelpers;
use App\Traits\NotificationHelpers;
use App\Traits\OrderHelpers;

class OrderObserver
{
    use NotificationHelpers, OrderHelpers;

    /**
     * Handle the Order "updating" event.
     *
     * @param Order $order
     * @return void
     */
    public function updating(Order $order)
    {
        $exOrder = Order::find($order->id);
       
        if($order?->order_status !== $exOrder?->order_status){
            $this->sendNotifyOfOrderStatusChanged($order, $exOrder);
        }

    }

    /**
     * Handle the Order "updated" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        if(isset($order) && $order['payment_method'] !=='cod') {
            // send confirmation sms/email/push notification of order
            $this->sendNotifyOfOrderConfirm($order->id);
       }

       if(isset($order) && $order['platform'] !=='manual' && $order['order_status'] === 'cancelled') {
            // send confirmation sms/email/push notification of order
            $this->deductionOrderRewardPoints($order);
        }

    }

}
