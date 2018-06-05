<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\Theory as TheoryResource;
use App\Http\Resources\Practice as PracticeResource;

class Variant extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'order' => $this->order,
        ];

        if ($this->relationLoaded('practices')) {
            $data['practices'] = PracticeResource::collection($this->practices);
        }

        return $data;
    }
}
