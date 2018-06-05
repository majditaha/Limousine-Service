<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\Section as SectionResource;
use App\Http\Resources\Variant as VariantResource;

class Discipline extends Resource
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
            'icon_file' => $this->icon_file,
        ];

        if ($this->relationLoaded('sections')) {
            $data['sections'] = SectionResource::collection($this->sections);
        }

        if ($this->relationLoaded('variants')) {
            $data['variants'] = VariantResource::collection($this->variants);
        }

        return $data;
    }
}
