<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExerciseEntry extends Model
{
    public $timestamps = false;

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function practice() {
        return $this->belongsTo(Practice::class);
    }
}
