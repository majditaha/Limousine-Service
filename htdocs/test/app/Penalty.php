<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penalty extends Model
{
    protected $fillable = ['teacher_id', 'message_id'];

    public function teachers() {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
