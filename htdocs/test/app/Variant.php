<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Variant extends Model
{
    protected $fillable = ['name', 'discipline_id', 'order'];

    protected static function boot() {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('order', 'asc');
        });
    }

    public function discipline() {
        return $this->belongsTo(Discipline::class);
    }

    public function practices() {
        return $this->hasMany(Practice::class);
    }
}
