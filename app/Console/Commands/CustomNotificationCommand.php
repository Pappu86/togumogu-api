<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Jobs\Notification\SendCustomNotification;
use App\Models\Message\CustomNotification;
use Illuminate\Support\Facades\Log;

class CustomNotificationCommand extends Command
{
    /**
     * The custom notification of the console command.
     *
     * @var string
     */
    protected $signature = 'customNotification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }  

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $instant_notifications = null;

        Log::info('after 2m');

        // $notifications = CustomNotification::query()
        //     ->where('status', '=', 'active')
        //     ->where('process_status', '=', 'processing');

        // if(isset($notifications)) {
        //    $instant_notifications = $notifications->where('scheduling_type', '=', 'now')->get(); 
        
        //    if(count($instant_notifications)) {
        //      foreach ($instant_notifications as $notification) { 
        //         SendCustomNotification::dispatch($notification);
        //      }
        //     }
        
        // }    
    }
}