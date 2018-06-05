<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    public function practice() {
        return $this->belongsTo(Practice::class);
    }

    public function userAnswers() {
        return $this->hasMany(AnswerUser::class);
    }

    public function getLastUserValue($userId) {
        $currentUserAnswer = $this->userAnswers->last(function ($userAnswer) use ($userId) {
            return $userAnswer->user_id == $userId;
        });

        if ($currentUserAnswer) {
            return $currentUserAnswer->value;
        }

        return null;
    }
}
