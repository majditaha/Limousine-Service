<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class MenuItem extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'alias' => $this->alias,
            'children' => self::collection($this->children),
        ];
    }
}
