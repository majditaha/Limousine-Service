<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\Discipline as DisciplineResource;

class DisciplineSubscription extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);

        if ($this->relationLoaded('discipline')) {
            $data['discipline'] = new DisciplineResource($this->discipline);
        }

        return $data;
    }
}
