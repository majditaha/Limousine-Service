<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnswerUser extends Model
{
    public function answer() {
        return $this->belongsTo(Answer::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
