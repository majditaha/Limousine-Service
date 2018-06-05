<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\Practice as PracticeResource;

class Theory extends Resource
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
            'text' => $this->text,
            'order' => $this->order,
            'section_id' => $this->section_id,
            'text_pdf' => $this->text_pdf,
        ];

        if ($this->relationLoaded('userProgresses')) {
            $data['finished'] = $this->isFinished(auth()->user()->id);
        }

        if ($this->relationLoaded('userTrainingProgresses')) {
            $data['finished'] = $this->isInTrainingFinished(auth()->user()->id);
        }

        if ($this->relationLoaded('practices')) {
            $data['practices'] = PracticeResource::collection($this->practices);
        }

        if ($this->relationLoaded('theoryPractices')) {
            $data['practices'] = PracticeResource::collection($this->theoryPractices);
        }

        return $data;
    }
}
