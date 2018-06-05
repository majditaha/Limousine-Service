<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Answer extends Resource
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
            'practice_id' => $this->practice_id,
            'value' => $this->value,
            'order' => $this->order,
            'correct' => $this->correct,
        ];

        if ($this->relationLoaded('userAnswers')) {
            $data['user_value'] = $this->getLastUserValue(auth()->user()->id);
        }

        return $data;
    }
}
