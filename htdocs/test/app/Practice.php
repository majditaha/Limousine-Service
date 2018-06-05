<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Practice extends Model
{
    use \Staskjs\LaravelUtils\Traits\CanSyncRelations;

    const ANSWER_TYPE_SINGLE_CHOICE = 'single_choice';
    const ANSWER_TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    const ANSWER_TYPE_SINGLE_VALUE = 'single_value';
    const ANSWER_TYPE_MULTIPLE_VALUE = 'multiple_value';
    const ANSWER_TYPE_TEXT = 'text';
    const ANSWER_TYPE_MATCHING = 'matching';

    const TYPE_THEORY = 'theory';
    const TYPE_PRACTICE = 'practice';
    const TYPE_TESTING = 'testing';

    protected $fillable = [
        'name', 'discipline_id', 'section_id', 'theory_id', 'order', 'text',
        'type', 'answer_type', 'xp_gain', 'hint', 'solution', 'variant_id', 'subtype_id',
        'text_pdf', 'hint_pdf', 'solution_pdf',
    ];

    protected static function boot() {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('order', 'asc');
        });
    }

    public function discipline() {
        return $this->belongsTo(Discipline::class);
    }

    public function section() {
        return $this->belongsTo(Section::class);
    }

    public function theory() {
        return $this->belongsTo(Theory::class);
    }

    public function answers() {
        return $this->hasMany(Answer::class);
    }

    public function userProgresses() {
        return $this->hasMany(PracticeProgress::class);
    }

    public function userResults() {
        return $this->hasMany(PracticeUser::class);
    }

    public function scopeFinished($query, $userId) {
        $query->whereHas('userProgresses', function ($subquery) use ($userId) {
            $subquery->whereUserId($userId);
        });
    }

    public function scopeWithLastAnswerResult($query, $correctness, $userId) {
        $subquery = \DB::table('practice_users')
            ->select('practice_users.correct')
            ->whereRaw('practice_users.practice_id = practices.id')
            ->whereUserId($userId)
            ->orderBy('created_at', 'DESC')
            ->take(1);

        $query->mergeBindings($subquery)->where(\DB::raw("({$subquery->toSql()})"), $correctness ? 'TRUE' : 'FALSE');
    }

    public function scopeOrderByLastAnswerDate($query, $userId) {
        // Select last answer date for every practice
        $subquery = \DB::table('practice_users')
            ->select('practice_users.created_at')
            ->whereRaw('practice_users.practice_id = practices.id')
            ->whereUserId($userId)
            ->orderBy('created_at', 'DESC NULLS LAST')
            ->take(1);

        $query->mergeBindings($subquery)->orderBy(\DB::raw("({$subquery->toSql()})"), 'DESC NULLS LAST');
    }

    public function scopeExcludeTextAnswerType($query) {
        $query->where('answer_type', '!=', self::ANSWER_TYPE_TEXT);
    }

    public function scopeWithScore($query, $userId) {
        $wrongSubquery = \DB::table('practice_users')
            ->selectRaw('COUNT(id)')
            ->whereRaw('practice_users.practice_id = practices.id')
            ->whereUserId($userId)
            ->whereCorrect(false);

        $correctSubquery = \DB::table('practice_users')
            ->selectRaw('COUNT(id)')
            ->whereRaw('practice_users.practice_id = practices.id')
            ->whereUserId($userId)
            ->whereCorrect(true);

        $query->mergeBindings($wrongSubquery)
            ->mergeBindings($correctSubquery)
            ->addSelect(\DB::raw("*, (({$wrongSubquery->toSql()}) - ({$correctSubquery->toSql()})) AS score"));
    }

    public function scopeOfPracticeType($query) {
        $query->whereType(self::TYPE_PRACTICE);
    }

    public function scopeOfTheoryType($query) {
        $query->whereType(self::TYPE_THEORY);
    }

    public static function getTypes() {
        return [
            self::TYPE_THEORY,
            self::TYPE_PRACTICE,
            self::TYPE_TESTING,
        ];
    }

    public static function getAnswerTypes() {
        return [
            self::ANSWER_TYPE_SINGLE_CHOICE,
            self::ANSWER_TYPE_MULTIPLE_CHOICE,
            self::ANSWER_TYPE_SINGLE_VALUE,
            self::ANSWER_TYPE_MULTIPLE_VALUE,
            self::ANSWER_TYPE_TEXT,
            self::ANSWER_TYPE_MATCHING,
        ];
    }

    public function syncAnswers($answers) {
        $this->syncRelation('answers', $answers, ['order', 'correct', 'value']);
    }

    public function isFinished($userId) {
        return $this->userProgresses->where('user_id', $userId)->isNotEmpty();
    }

    // Check if given answers are correct
    public function isCorrectAnswer($answers) {
        // If user has not given any answer, it means wrong
        if (empty($answers)) {
            return false;
        }

        // For text value there are no correct answer to compare with
        // Teacher will check this
        // Null will indicate that user has given an answer, but it is not yet checked
        if ($this->answer_type == self::ANSWER_TYPE_TEXT) {
            return true;
        }

        $correct = true;

        $numOfCorrectAnswers = $this->answers->where('correct', true)->count();

        foreach ($answers as $answerId => $value) {
            $answer = $this->answers()->whereId($answerId)->first();

            if (!$answer) {
                continue;
            }

            $hasCorrectNumberOfAnswers = true;

            if ($this->answer_type == self::ANSWER_TYPE_MULTIPLE_VALUE ||
                $this->answer_type == self::ANSWER_TYPE_MULTIPLE_CHOICE ||
                $this->answer_type == self::ANSWER_TYPE_MATCHING
            ) {
                $hasCorrectNumberOfAnswers = count($answers) == $numOfCorrectAnswers;
            }

            if (!(strtolower($answer->value) == strtolower($value) && $answer->correct && $hasCorrectNumberOfAnswers)) {
                $correct = false;
                break;
            }
        }

        return $correct;
    }

    // Create record indicating that user had answered this practice
    public function setFinished($user) {
        if (!$user->finishedPractices()->wherePracticeId($this->id)->exists()) {
            $user->finishedPractices()->create(['practice_id' => $this->id]);
        }
    }

    // Create new record indicating user given answer to current practice is correct or not
    public function setCorrectness($user, $correct, $isTraining = false) {
        $user->practiceAnswers()->create([
            'practice_id' => $this->id,
            'correct' => $correct,
            'is_training' => $isTraining,
        ]);
    }

    // Get 6 practices
    // Formula 2+2+2
    // First 2 are most difficult for user
    // Next 2 are oldest
    // Next 2 are random from those that user finished already
    public static function getSmart($user) {
        $with = 'answers';

        $worstAnsweredPractices = Practice::with($with)
            ->withScore($user->id)
            ->excludeTextAnswerType()
            ->finished($user->id)
            ->ofPracticeType()
            ->orderBy('score', 'DESC')
            ->take(2)
            ->get();

        $practiceIds = $worstAnsweredPractices->pluck('id');

        $oldestAnsweredPractices = Practice::with($with)
            ->excludeTextAnswerType()
            ->finished($user->id)
            ->withLastAnswerResult(true, $user->id)
            ->whereNotIn('id', $practiceIds)
            ->ofPracticeType()
            ->orderByLastAnswerDate($user->id)
            ->take(2)
            ->get();

        $practiceIds = collect([$worstAnsweredPractices, $oldestAnsweredPractices])->flatten()->pluck('id');

        $randomPractices = Practice::with($with)
            ->excludeTextAnswerType()
            ->finished($user->id)
            ->ofPracticeType()
            ->whereNotIn('id', $practiceIds)
            ->take(2)
            ->get();

        $practices = collect([$worstAnsweredPractices, $oldestAnsweredPractices, $randomPractices])
            ->flatten();

        $total = $practices->count();

        $additionalPractices = [];

        // In case if there are not all of required practices found,
        // fill in random practices until there are 6 practices in total
        if ($total < 6) {
            $practiceIds = $practices->pluck('id');
            $additionalPractices = self::with($with)
                ->excludeTextAnswerType()
                ->ofPracticeType()
                ->whereNotIn('id', $practiceIds)
                ->inRandomOrder()
                ->take(6 - $total)
                ->get();
        }

        $practices = $practices->merge($additionalPractices);

        return $practices;
    }

    // Get 5 practices for selected section
    // For the first time get 5 random practices for section
    // For all next times get 3 random practices for section (excluding ones that user already had answered),
    // + 1 practice from any other section that user made wrong answer to, worst first
    // + 1 practice from any other section that user made correct answer to longest time ago
    //
    // If there are no practices left for selected section, take what is left
    // If there are no practices left for other sections, take practices from selected section (if any left)
    public static function getTrainings($section, $user) {
        $with = 'answers.userAnswers';

        $allPracticeIds = $section->trainings()
            ->excludeTextAnswerType()
            ->pluck('id');

        $finishedPracticeIds = $user->finishedPractices()
            ->whereIn('practice_id', $allPracticeIds)
            ->pluck('practice_id');

        $notFinishedPractices = $section->trainings()
            ->excludeTextAnswerType()
            ->with($with)
            ->whereNotIn('id', $finishedPracticeIds)
            ->inRandomOrder()
            ->get();

        $firstTime = !$section->isFinished($user->id);

        $data = [];

        $numOfPractices = 5;

        if (!$firstTime) {
            // Get practices, divide them by subtypes,
            // get subtype with the most score (most incorrect answers),
            // select random practice from it
            $worstAnsweredPractices = Practice::with($with)
                ->withScore($user->id)
                ->excludeTextAnswerType()
                ->finished($user->id)
                ->where('section_id', '!=', $section->id)
                ->orderBy('score', 'DESC')
                ->get()
                // HACK: better to put filtering in select statement,
                // but postgres does not allow to use custom fields in WHERE,
                // WITH statement will help here, probably
                ->filter(function ($practice) {
                    return $practice->score > 0;
                })
                ->groupBy('subtype_id')
                ->sortByDesc
                ->sum('score')
                ->values()
                ->first();

            $worstAnsweredPractice = null;

            if ($worstAnsweredPractices) {
                $worstAnsweredPractice = $worstAnsweredPractices->random();
                $data[] = $worstAnsweredPractice;
                $numOfPractices--;
            }

            $oldestAnsweredPractice = Practice::with($with)
                ->excludeTextAnswerType()
                ->finished($user->id)
                ->withLastAnswerResult(true, $user->id)
                ->where('section_id', '!=', $section->id)
                ->where('id', '!=', $worstAnsweredPractice ? $worstAnsweredPractice->id : null)
                ->orderByLastAnswerDate($user->id)
                ->first();

            if ($oldestAnsweredPractice) {
                $data[] = $oldestAnsweredPractice;
                $numOfPractices--;
            }
        }

        $notFinished = $notFinishedPractices->take($numOfPractices);

        // Insert not finished tasks at the beginning of the array
        array_unshift($data, $notFinished);

        $data = collect($data)->flatten();

        return $data;
    }

    public function getTextPdfAttribute($value) {
        return json_decode($value, true);
    }

    public function setTextPdfAttribute($value) {
        $this->attributes['text_pdf'] = json_encode($value);
    }

    public function getHintPdfAttribute($value) {
        return json_decode($value, true);
    }

    public function setHintPdfAttribute($value) {
        $this->attributes['hint_pdf'] = json_encode($value);
    }

    public function getSolutionPdfAttribute($value) {
        return json_decode($value, true);
    }

    public function setSolutionPdfAttribute($value) {
        $this->attributes['solution_pdf'] = json_encode($value);
    }
}
