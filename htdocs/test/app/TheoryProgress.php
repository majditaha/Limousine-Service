<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TheoryProgress extends Model
{
    protected $fillable = ['theory_id', 'is_training'];
}
