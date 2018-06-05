<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\Theory as TheoryResource;
use App\Http\Resources\Practice as PracticeResource;

class Section extends Resource
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

        if ($this->relationLoaded('userProgresses')) {
            $data['finished'] = $this->isFinished(auth()->user()->id);
        }

        $data['can_pass_again'] = $this->canPassAgain(auth()->user());

        if ($this->relationLoaded('theoriesWithoutPractices')) {
            $data['theories'] = TheoryResource::collection($this->theoriesWithoutPractices);
            $data['theories_finished'] = $this->areTheoriesFinished($this->theoriesWithoutPractices, auth()->user(), true);
        }

        if ($this->relationLoaded('theoriesWithTheoryPractices')) {
            $data['theories'] = TheoryResource::collection($this->theoriesWithTheoryPractices);
            $data['theories_finished'] = $this->areTheoriesFinished($this->theoriesWithTheoryPractices, auth()->user(), true);
        }

        if ($this->relationLoaded('theories')) {
            $data['theories'] = TheoryResource::collection($this->theories);
            $data['theories_finished'] = $this->areTheoriesFinished($this->theories, auth()->user(), false);
        }

        if ($this->relationLoaded('practicesOfPracticeType')) {
            $data['practices'] = PracticeResource::collection($this->practicesOfPracticeType);
            $data['practices_finished'] = $this->arePracticesFinished($this->practicesOfPracticeType, auth()->user());
        }

        if ($this->relationLoaded('practicesOfTheoryType')) {
            $data['practices'] = PracticeResource::collection($this->practicesOfTheoryType);
            $data['practices_finished'] = $this->arePracticesFinished($this->practicesOfTheoryType, auth()->user());
        }

        if ($this->relationLoaded('practices')) {
            $data['practices'] = PracticeResource::collection($this->practices);
            $data['practices_finished'] = $this->arePracticesFinished($this->practices, auth()->user());
        }

        if ($this->relationLoaded('trainings')) {
            $data['trainings_finished'] = $this->areTrainingsFinished(auth()->user());
            $data['trainings_answer_results'] = $this->getTrainingsAnswerResults(auth()->user());
        }

        return $data;
    }
}
