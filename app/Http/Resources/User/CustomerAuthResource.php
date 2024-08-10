<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Http\Resources\Child\ChildResource;
use App\Http\Resources\Corporate\CompanyResource;
use App\Http\Resources\Corporate\EmployeeApiResource;
use App\Http\Resources\Reward\ReferralApiResource;
use App\Http\Resources\Reward\RewardApiResource;
use App\Traits\CommonHelpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerAuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $commonHelpers = new CommonHelpers;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'avatar' => $this->avatar,
            'blood_group' => $this->blood_group,
            'gender' => $this->gender?:null,
            'createdAt' => $this->created_at,
            'parent_type' => $this->parent_type,
            'primary_language' => $this->primary_language,
            'religion' => $this->religion,
            'education' => $this->education,
            'date_of_birth' => $this->date_of_birth,
            'age' => $this->getAge($this->date_of_birth),
            'company' => $this->company?new CompanyResource($this->company): null,
            'position' => $this->position,
            'profession' => $this->profession,
            'location' => $this->location?->name ?: '',
            'area_id' => $this->area_id,
            'shipping_email' => $this->shipping_email,
            'current_latitude' => $this->current_latitude,
            'current_longitude' => $this->current_longitude,
            'current_location_name' => $this->current_location_name,
            'profile_progress' => $commonHelpers->getProfileProgress($this),
            'childrens' => ChildResource::collection($this->children),
            'employee' => new EmployeeApiResource($this->employee),
            'reward' => new RewardApiResource($this->reward),
            'referral' => new ReferralApiResource($this->referral),
            'settings' => collect($this->getSettings($this->settings))->groupBy('category')->toArray(),
        ];
    }

    /**
     * @param $date_of_birth
     * @return string
     */
    private function getAge($date_of_birth): string
    {
        $years = Carbon::parse($date_of_birth)->age;
        $age_unite = $years > 1 ? 'years' : 'year';

        return "$years $age_unite";
    }
    
    private function getSettings($settings)
    {
        $commonHelpers = new CommonHelpers;
        $customer = Auth::user();
        $default_Settings = $this->getDefaultSettings();
        $new_settings = array();
        $isEnabledAllPushNotification = $commonHelpers->isSettingEnabled($customer, 'push_notification', 'all');

        foreach($default_Settings as $d_setting) {
          $setting = collect($settings)->where('category', $d_setting['category'])
                    ->where('key', $d_setting['key'])->first();

            if($d_setting['category'] === 'push_notification') {
                $d_setting['is_enabled'] = ($d_setting['key'] === 'all')?true:$isEnabledAllPushNotification;
            }

          if(isset($setting)){
              $d_setting['id'] = $setting?->id?:null;
              $d_setting['value'] = $setting->value;
              array_push($new_settings, $d_setting);
          } else {
              array_push($new_settings, $d_setting);
          }
        }

        return $new_settings;
    }

    private function getDefaultSettings()
    {
        $customer_id = Auth::id();
        return [
                    [
                        'customer_id' => $customer_id,
                        'key' => 'order',
                        'category' => 'push_notification',
                        'type' => 'boolean',
                        'value' => 'true',
                        'context' => 'order',
                    ],
                    [
                        'customer_id' => $customer_id,
                        'key' => 'comment',
                        'category' => 'push_notification',
                        'type' => 'boolean',
                        'value' => 'true',
                        'context' => 'comment',
                    ],
                    [
                        'customer_id' => $customer_id,
                        'key' => 'comment_reaction',
                        'category' => 'push_notification',
                        'type' => 'boolean',
                        'value' => 'true',
                        'context' => 'comment',
                    ],
                    [
                        'customer_id' => $customer_id,
                        'key' => 'post_reaction',
                        'category' => 'push_notification',
                        'type' => 'boolean',
                        'value' => 'true',
                        'context' => 'post',
                    ],
                    [
                        'customer_id' => $customer_id,
                        'key' => 'comment_reply',
                        'category' => 'push_notification',
                        'type' => 'boolean',
                        'value' => 'true',
                        'context' => 'comment',
                    ],
                    [
                        'customer_id' => $customer_id,
                        'key' => 'invitation_reward',
                        'category' => 'push_notification',
                        'type' => 'boolean',
                        'value' => 'true',
                        'context' => 'reward',
                    ],
                    [
                        'customer_id' => $customer_id,
                        'key' => 'all',
                        'category' => 'push_notification',
                        'type' => 'boolean',
                        'value' => 'true',
                        'context' => 'push_notification',
                    ],
                ];

    }

}