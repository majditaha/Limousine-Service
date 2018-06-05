<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DisciplineUser extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'discipline_id', 'subscription_ends_at',
    ];

    public function discipline() {
        return $this->belongsTo(Discipline::class);
    }
}
