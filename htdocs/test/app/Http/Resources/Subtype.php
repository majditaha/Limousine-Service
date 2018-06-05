<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Subtype extends Resource
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
            'section_id' => $this->section_id,
        ];

        return $data;
    }
}
