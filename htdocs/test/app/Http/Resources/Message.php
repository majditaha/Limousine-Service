<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\Practice as PracticeResource;

class Message extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $history = $this->getHistoryQuery()->with('images', 'files')->get();
        return [
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'type' => $this->type,
            'uid' => $this->uid,
            'rating' => $this->rating,
            'history' => MessageHistoryItem::collection($history),
            'practice' => new PracticeResource($this->practice),
            $this->mergeWhen(auth()->user()->isTeacher(), [
                'price' => $this->getPrice(auth()->user()),
                'taken_at' => $this->taken_at,
                'finished_at' => $this->finished_at,
            ]),
        ];
    }
}
