<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Jobs\Customer\AddCustomerRegistrationGreeting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerRegistrationNotificationCommand extends Command
{
    /**
     * The custom notification of the console command.
     *
     * @var string
     */
    protected $signature = 'CustomerRegistrationNotificationCommand';

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
        DB::table('custom_notifications')->where('notification_type', 'schedule')
            ->where('activity', 'registration')
            ->where('status', 'active')
            ->get()
            ->map(function ($notification) {
                if(isset($notification)) {
                    AddCustomerRegistrationGreeting::dispatch($notification);
                }
            });
    }
}