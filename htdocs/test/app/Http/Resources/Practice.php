<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\Answer as AnswerResource;

class Practice extends Resource
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
            'discipline_id' => $this->discipline_id,
            'answer_type' => $this->answer_type,
            'type' => $this->type,
            'text' => $this->text,
            'theory_id' => $this->theory_id,
            'section_id' => $this->section_id,
            'order' => $this->order,
            'hint' => $this->hint,
            'solution' => $this->solution,
            'text_pdf' => $this->text_pdf,
            'hint_pdf' => $this->hint_pdf,
            'solution_pdf' => $this->solution_pdf,
        ];

        if ($this->relationLoaded('answers')) {
            $data['answers'] = AnswerResource::collection($this->answers);
            if ($this->answers->count() && $this->answers->first()->relationLoaded('userAnswers')) {
                $selectedAnswers = $this->answers->reduce(function ($carry, $answer) {
                    $value = $answer->getLastUserValue(auth()->user()->id);
                    if (!is_null($value)) {
                        $carry[$answer->id] = $value;
                    }
                    return $carry;
                }, []);

                if ($selectedAnswers) {
                    $data['selected_answers'] = $selectedAnswers;
                }
            }
        }

        if ($this->relationLoaded('userProgresses')) {
            $data['finished'] = $this->isFinished(auth()->user()->id);
        }

        return $data;
    }
}
