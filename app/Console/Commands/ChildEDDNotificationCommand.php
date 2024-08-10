<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Jobs\Child\AddChildEDDGreeting;
use Illuminate\Support\Facades\DB;

class ChildEDDNotificationCommand extends Command
{
    /**
     * The custom notification of the console command.
     *
     * @var string
     */
    protected $signature = 'ChildEDDNotificationCommand';

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
            ->where('activity', 'edd')
            ->where('status', 'active')
            ->get()
            ->map(function ($notification) {
                if(isset($notification) && $notification?->id) {
                    AddChildEDDGreeting::dispatch($notification);
                }
            });

    }
}