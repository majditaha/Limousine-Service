<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class MessageHistoryItem extends Resource
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
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'content' => $this->content,
            'rating' => $this->rating,
            'read_at' => $this->read_at,
            'rating_explained' => !empty($this->rating_message_id),
        ];

        if ($this->relationLoaded('sender')) {
            $data['sender'] = [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
                'photo' => $this->sender->photo,
            ];
        }

        if ($this->relationLoaded('images')) {
            $data['images'] = $this->images;
        }

        if ($this->relationLoaded('files')) {
            $data['files'] = $this->files;
        }

        return $data;
    }
}
