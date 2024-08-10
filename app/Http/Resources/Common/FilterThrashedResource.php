<?php

namespace App\Http\Resources\Common;

use App\Http\Resources\CategoryTreeChildResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class FilterThrashedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $parentName='-';
        if($this->parent_id){
            $parentFilter = DB::table('filter_translations')->where('filter_id', '=', $this->parent_id)->first();
            $parentName=$parentFilter->name;
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'parent_name' => $parentName,
            'parent_id' => $this->parent_id
        ];
    }
}
