<?php

namespace App\Jobs\Customer;

use App\Models\Corporate\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AddCorporateEmployee implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue,
     Queueable, SerializesModels;

    /**
     * @var
     */
    protected $customer;
    protected $customer_id;
    protected $referral;
    protected $referral_uid;
    protected $referral_type;

    public function __construct($customer, $referral, $link_data)
    {
        $this->customer = $customer;
        $this->customer_id = $customer?->id?:'';
        $this->referral = $referral?:'';
        $this->referral_uid = $link_data['uid']?:'';
        $this->referral_type = $link_data['type']?:'';      
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      
        $partnership_id = $this->referral?->partnership?->id?:"";
        $company_id = $this->referral?->partnership?->company_id?:"";
        $group_id = $this->referral?->partnership?->group_id?:"";

        if(isset($partnership_id)) {
            $insertData = [
                'status' => 'active',
                'is_registered'=> '1',
                'company_id' => $company_id,
                'group_id' => $group_id,
                'name' => $this->customer?->name?:''
            ];

            if(isset($this->customer?->mobile)) {
                $insertData['phone'] = $this->customer?->mobile;
            }
            
            if(isset($this->customer?->email)) {
                $insertData['email'] = $this->customer?->email;
            }

            $employee = Employee::create($insertData);

            // Updated customer Employee id after employee created
            $this->customer->update([
                'employee_id' => $employee['id'],
                'company_id' => $company_id,
            ]);

        }

    }

}
