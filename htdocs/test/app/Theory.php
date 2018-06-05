<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Theory extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name', 'section_id', 'order', 'text', 'text_pdf',
    ];

    protected static function boot() {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('id', 'asc');
        });
    }

    public function section() {
        return $this->belongsTo(Section::class);
    }

    public function practices() {
        return $this->hasMany(Practice::class);
    }

    public function theoryPractices() {
        return $this->hasMany(Practice::class)->ofTheoryType();
    }

    public function userProgresses() {
        return $this->hasMany(TheoryProgress::class)->whereIsTraining(false);
    }

    public function userTrainingProgresses() {
        return $this->hasMany(TheoryProgress::class)->whereIsTraining(true);
    }

    public function practicesFinished($userId) {
        $finishedPracticesCount = $this->theoryPractices->map(function ($practice) use ($userId) {
            return $practice->isFinished($userId);
        })->filter()->count();

        return $finishedPracticesCount == $this->theoryPractices->count();
    }

    public function isFinished($userId) {
        return $this->userProgresses->where('user_id', $userId)->isNotEmpty();
    }

    public function isInTrainingFinished($userId) {
        return $this->userTrainingProgresses->where('user_id', $userId)->isNotEmpty();
    }

    public function getTextPdfAttribute($value) {
        return json_decode($value, true);
    }

    public function setTextPdfAttribute($value) {
        $this->attributes['text_pdf'] = json_encode($value);
    }
}
