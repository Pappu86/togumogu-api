<?php

namespace App\Traits;

use Carbon\Carbon;
trait TemplateHelpers {

    /**
     * @param array
     * @param string
     * @return string
     */
    public function getDynamicContent($variables, $content): string
    {
 
        if(count($variables)) {
            foreach($variables as $key=>$parameter)
            {
              $content = str_replace('{{'.$key.'}}', $parameter, $content);
            }
        }

        return $content;
    }

    /**
     * @param string
     */
    public function getVariables($category, $options)
    {
        $variables = [];
        $customer = $options? $options['customer']:null;
        $child = $options? $options['child']:null;

        if($category === 'customer' && $customer) {
            $variables['customer_name'] = $customer['name']?:null;
            $variables['customer_gender'] = $customer['gender']?:null;
            $variables['customer_age'] = $this->getAge($customer['date_of_birth'])?:null;
            $variables['customer_dob'] = $customer['date_of_birth']?:null;
            $variables['child_name'] = $child?->name?:null;
            $variables['child_dob'] = $child?->date_of_birth?:null;
            $variables['child_edd'] = $child?->expecting_date? Carbon::parse($child->expecting_date)->format('M d Y'):null;
            $variables['child_gender'] = $child?->gender?:null;
            $variables['child_age'] = $this->getAge($child?->date_of_birth)?:null;
        }

        return $variables;
    }

    /**
     * @param string
     */
    private function getAge($dob)
    {
        return Carbon::parse($dob)->age;
    }
}