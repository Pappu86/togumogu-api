<?php

namespace App\Http\Resources\Child;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Models\Child\Doctor;
use App\Http\Resources\Child\ChildClassResource;
use App\Http\Resources\Child\HospitalResource;
use App\Http\Resources\Child\SchoolResource;
use App\Http\Resources\Child\DoctorResource;

class ChildResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'age' => $this->getAge($this->date_of_birth, 'age'),
            'age_in_day' => $this->getAge($this->date_of_birth, 'day'),
            'age_in_week' => $this->getAge($this->date_of_birth, 'week'),
            'age_in_month' => $this->getAge($this->date_of_birth, 'month'),
            'age_in_year' => $this->getAge($this->date_of_birth, 'year'),
            'total_age_days' => $this->getAge($this->date_of_birth, 'total_days'),
            'religion' => $this->religion,
            'blood_group' => $this->blood_group,
            'avatar' => $this->avatar,
            'parent_id' => $this->parent_id,
            'parent_status' => $this->parent_status?:'parent',
            'expecting_date' => $this->expecting_date,
            'pregnancy_days' => $this->getPregnancyDays($this->expecting_date, 'day'),
            'pregnancy_in_week' => $this->getPregnancyDays($this->expecting_date, 'week'),
            'pregnancy_due_week' => $this->getPregnancyDays($this->expecting_date, 'due'),
            'createdAt' => $this->created_at,
            'birth_registration_number' => $this->birth_registration_number,
            'birth_location' => $this->birth_location,
            'birth_hospital' => new HospitalResource($this->hospital),
            'school' => new SchoolResource($this->school),
            'child_class' => new ChildClassResource($this->class),
            'doctors' => $this->getDoctors($this->doctor_id),
            'parent_name' => $this->parent?->name?:'null',
            'is_default' => $this->is_default,
        ];
    }

    /**
     * @param $doctor_ids
     */
    private function getDoctors($doctor_ids)
    {
        if($doctor_ids) {
            $doctor_ids = is_string($doctor_ids)? json_decode($doctor_ids):$doctor_ids;
            $doctor = Doctor::whereIn('id', $doctor_ids)->get();
            return DoctorResource::collection($doctor);
        } else return [];
    }

    /**
     * @param $date_of_birth
     */
    private function getAge($date_of_birth, $type)
    {
        
        $age_calculate = Carbon::parse($date_of_birth)->diff(Carbon::now());   

        if(isset($type) && $type === 'age') {
            $age = "";
            $day = $age_calculate->format('%d');
            $week = ceil($day/7);
            $month = $age_calculate->format('%m');
            $year = $age_calculate->format('%y');
            
            $year_unit = $year > 1 ? 'years' : 'year';
            $month_unit = $month > 1 ? 'months' : 'month';
            $week_unit = $week > 1 ? 'weeks' : 'week';
            $year_with_unit = $year? "$year $year_unit":'';
            $month_with_unit = $month? " $month $month_unit":'';
            $week_with_unit = $week? " $week $week_unit":'';

            return "$year_with_unit$month_with_unit$week_with_unit";

        } else if(isset($type) && $type === 'year') {
            return ceil($age_calculate->format('%y')); 
        } else if(isset($type) && $type === 'month') {
            return ceil($age_calculate->format('%m')); 
        } else if(isset($type) && $type === 'week') {
            $days = $age_calculate->format('%d');
            return ceil($days/7); 
        } else if(isset($type) && $type === 'day') {
            return ceil($age_calculate->format('%d'));   
        } else if(isset($type) && $type === 'total_days') {
            return ceil($age_calculate->format('%a'));   
        }
    }

    /**
     * @param $date_of_birth
     */
    private function getPregnancyDays($expecting_date, $type)
    {
        $now = Carbon::now();
        $date = Carbon::parse($expecting_date);
        $pregnancy_duration = 280;

        $due_delivery_days = $date->diffInDays($now);
        $total_pregnancy_days = $pregnancy_duration - $due_delivery_days;


        if(isset($type) && $type === 'day') {
            
            if($due_delivery_days>$pregnancy_duration) return $due_delivery_days;
            else return $total_pregnancy_days;

        } else if(isset($type) && $type === 'week') {
            $week = ceil($total_pregnancy_days/7);
            $week_unit = $week > 1 ? 'weeks' : 'week';
            $week_with_unit = $week? "$week $week_unit":'';
            return $week_with_unit;

        } else if(isset($type) && $type === 'due') {
            $week = ceil($due_delivery_days/7);
            $week_unit = $week > 1 ? 'weeks' : 'week';
            $week_with_unit = $week? "$week $week_unit":'';
            return $week_with_unit;
        }
    }
}
