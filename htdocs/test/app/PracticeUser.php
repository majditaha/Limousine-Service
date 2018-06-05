<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PracticeUser extends Model
{
    protected $fillable = ['practice_id', 'correct', 'is_training'];
}
