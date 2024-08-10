<?php

namespace App\Http\Resources\Common;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactUsSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'company' => $this['company_title'],
            'company_bn' => $this['company_title_bn'],
            'phone' => $this['contact_phone'],
            'partner_contact_phone' => $this['partner_contact_phone'],
            'email' => $this['contact_email'],
            'address' => $this['contact_address'],
            'address_bn' => $this['contact_address_bn'],
            'description' => $this['app_description'],
            'description_bn' => $this['app_description_bn'],
        ];
    }
}
