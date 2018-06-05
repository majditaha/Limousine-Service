<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Section extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name', 'discipline_id', 'order',
    ];

    protected static function boot() {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('id', 'asc');
        });
    }

    public function discipline() {
        return $this->belongsTo(Discipline::class);
    }

    public function theories() {
        return $this->hasMany(Theory::class);
    }

    public function theoriesWithoutPractices() {
        return $this->hasMany(Theory::class)->whereDoesntHave('practices');
    }

    public function theoriesWithTheoryPractices() {
        return $this->hasMany(Theory::class);
    }

    public function practices() {
        return $this->hasMany(Practice::class);
    }

    public function trainings() {
        return $this->hasMany(Practice::class)->whereType(Practice::TYPE_PRACTICE);
    }

    public function practicesOfPracticeType() {
        return $this->hasMany(Practice::class)->ofPracticeType();
    }

    public function practicesOfTheoryType() {
        return $this->hasMany(Practice::class)->ofTheoryType();
    }

    public function userProgresses() {
        return $this->hasMany(SectionProgress::class);
    }

    public function areTheoriesFinished($theories, $user, $isTraining) {
        $finishedTheories = $theories->map(function ($theory) use ($user, $isTraining) {
            return $isTraining
                ? $theory->isInTrainingFinished($user->id)
                : $theory->isFinished($user->id);
        })->filter();

        return $finishedTheories->count() == $theories->count();
    }

    public function arePracticesFinished($practices, $user) {
        $finishedPractices = $practices->map(function ($practice) use ($user) {
            return $practice->isFinished($user->id);
        })->filter();

        return $finishedPractices->count() == $practices->count();
    }

    public function getCorrectnessRating($practices, $user) {
        $ids = $practices->pluck('id');

        $correctAnsweredPractices = Practice::withLastAnswerResult(true, $user->id)
            ->finished($user->id)
            ->whereIn('id', $ids)
            ->count();

        $wrongAnsweredPractices = Practice::withLastAnswerResult(false, $user->id)
            ->finished($user->id)
            ->whereIn('id', $ids)
            ->count();

        $total = $correctAnsweredPractices + $wrongAnsweredPractices;

        if ($total == 0) {
            return 0;
        }

        return $correctAnsweredPractices / $total * 5;
    }

    // Create record indicating that user had finished this section
    public function setFinished($user) {
        $existing = $user->finishedSections()->whereSectionId($this->id)->first();
        if ($existing) {
            $existing->touch();
        }
        else {
            $user->finishedSections()->create(['section_id' => $this->id]);
        }
    }

    public function areTrainingsFinished($user) {
        $count = \DB::table('practice_users')
            ->whereIn('practice_id', function ($practicesQuery) {
                $practicesQuery->select('id')
                    ->from(with(new Practice)->getTable())
                    ->where('practices.section_id', $this->id)
                    ->where('practices.type', Practice::TYPE_PRACTICE);
            })
            ->whereUserId($user->id)
            ->whereIsTraining(true)
            ->count();

        return $count > 0;
    }

    public function getTrainingsAnswerResults($user) {
        return $user->practiceAnswers
            ->whereIn('practice_id', $this->trainings->pluck('id'))
            ->pluck('correct');
    }

    public function isFinished($userId) {
        return $this->userProgresses->where('user_id', $userId)->isNotEmpty();
    }

    public function canPassAgain($user) {
        $lastRenewedSubscriptionAt = $user->getLastRenewedSubscriptionAt($this->discipline);
        $lastFinished = $this->userProgresses()->whereUserId($user->id)->orderBy('updated_at', 'DESC')->first();

        $lastFinishedAt = $lastFinished ? $lastFinished->updated_at : null;

        return $lastRenewedSubscriptionAt > $lastFinishedAt;
    }
}
