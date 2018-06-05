<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\HasApiToken;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use App\Notifications\MailResetPasswordToken;

class User extends Authenticatable implements CanResetPassword
{
    use Notifiable;
    use HasApiToken;
    use SoftDeletes;
    use CanResetPasswordTrait;

    const TYPE_USER = 'user';
    const TYPE_TEACHER = 'teacher';
    const TYPE_ADMIN = 'admin';

    const SUBTYPE_PUPIL = 'pupil';
    const SUBTYPE_STUDENT = 'student';
    const SUBTYPE_PARENT = 'parent';
    const SUBTYPE_TEACHER = 'teacher';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'password', 'email', 'photo', 'gender', 'birth_date', 'role',
        'city_id', 'school_id', 'grade', 'grade_name', 'active', 'phone',
        'passport_file', 'empl_history_file', 'subtype', 'accepted_agreement',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token', 'vkontakte_id',
    ];

    protected $dates = ['deleted_at', 'desired_hours_set_at'];

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->email_confirmed) {
                $model->generateEmailToken();
            }
        });
    }

    public function scopeInactive($query) {
        return $query->whereActive(false);
    }

    public function disciplines() {
        return $this->belongsToMany(Discipline::class, 'discipline_users');
    }

    public function disciplineSubscriptions() {
        return $this->hasMany(DisciplineUser::class);
    }

    public function finishedTheories() {
        return $this->hasMany(TheoryProgress::class)->whereIsTraining(false);
    }

    public function finishedTrainingTheories() {
        return $this->hasMany(TheoryProgress::class)->whereIsTraining(true);
    }

    public function finishedSections() {
        return $this->hasMany(SectionProgress::class);
    }

    public function checkRequests() {
        return $this->hasMany(Message::class, 'from_user_id')->whereType(Message::TYPE_CHECK_REQUEST);
    }

    public function checkTests() {
        return $this->hasMany(Message::class, 'from_user_id')->whereType(Message::TYPE_CHECK_TEST);
    }

    public function finishedPractices() {
        return $this->hasMany(PracticeProgress::class);
    }

    public function practiceAnswers() {
        return $this->hasMany(PracticeUser::class);
    }

    public function answers() {
        return $this->hasMany(AnswerUser::class);
    }

    public function purchases() {
        return $this->hasMany(Purchase::class);
    }

    public function isAdmin() {
        return $this->role == self::TYPE_ADMIN;
    }

    public function isTeacher() {
        return $this->role == self::TYPE_TEACHER;
    }

    public function isUser() {
        return $this->role == self::TYPE_USER;
    }

    public static function getTypes() {
        return [
            self::TYPE_USER,
            self::TYPE_TEACHER,
            self::TYPE_ADMIN,
        ];
    }

    public static function getSubTypes() {
        return [
            self::SUBTYPE_PUPIL,
            self::SUBTYPE_STUDENT,
            self::SUBTYPE_PARENT,
            self::SUBTYPE_TEACHER,
        ];
    }

    /**
     * Send a password reset email to the user
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MailResetPasswordToken($token, $this->email));
    }

    public function hasPassword() {
        return !empty($this->password);
    }

    public function takenMessages() {
        return $this->hasMany(Message::class, 'teacher_id')->whereType(Message::TYPE_CHECK_REQUEST);
    }

    public function receivedMessages() {
        return $this->hasMany(Message::class, 'to_user_id');
    }

    public function penalties() {
        if ($this->isTeacher()) {
            return $this->hasMany(Penalty::class, 'teacher_id');
        }
    }

    public function receivedRatings() {
        if ($this->isTeacher()) {
            return $this->hasMany(Message::class, 'teacher_id')->whereNotNull('rating');
        }
    }

    public function getRating() {
        if ($this->isTeacher()) {
            $scores = $this->receivedRatings()->orderBy('id','desc')->limit(500)->get();
            $count = $scores->count('rating');
            $stars = $scores->groupBy('rating')
                    ->map(function ($item, $key) use ($count) {
                        return number_format(count($item) / $count * 100) ;
                    });
            $penalty = $this->penalties()->count() * \App\Setting::getValue('teacherDelayPenalty') / 100;
            return [
                    'avg' => number_format(max($scores->avg('rating') - $penalty, 0), 2, '.', ''),
                    'stars' => $stars,
                ];
        }
    }

    public function getDisciplineIdsAttribute() {
        return $this->disciplineSubscriptions->pluck('discipline_id');
    }

    // Check if user has all required fields checked before he can work
    public function isValid() {
        if ($this->isAdmin()) {
            return true;
        }

        $fields = ['email', 'gender', 'subtype', 'accepted_agreement'];

        if ($this->isTeacher()) {
            $fields = ['email', 'passport_file', 'empl_history_file', 'discipline_ids'];
        }

        $data = $this->attributes;
        $data['discipline_ids'] = $this->discipline_ids->all();

        return collect($data)
            ->only($fields)
            ->values()
            ->map(function ($val) {
                return is_null($val) || (!is_integer($val) && empty($val));
            })
            ->filter()
            ->isEmpty();
    }

    public function sendMessageEmail($message) {
        if ($this->email_confirmed) {
            \Mail::to($this)->send(new \App\Mail\MessageSent($message));
        }
    }

    public function sendWelcomeEmail() {
        \Mail::to($this)->send(new \App\Mail\Welcome($this));
    }

    public function generateEmailToken() {
        $this->email_token = str_random(20);
    }

    public function confirm() {
        $this->email_confirmed = true;
        $this->email_token = null;
        $this->save();
    }

    public function isNeededToAskDesiredMinutes() {
        return $this->desired_minutes_to_spend == 0 ||
            \Carbon\Carbon::parse($this->desired_minutes_set_at)->diffInMinutes(\Carbon\Carbon::now()) >= 24 * 60;
    }

    public function canUpdatePresence() {
        return empty($this->presence_updated_at) ||
            \Carbon\Carbon::parse($this->presence_updated_at)->diffInMinutes(\Carbon\Carbon::now()) >= 1;
    }

    public function saveAnswers($practice, $answers, $isTraining = false) {
        $correct = $practice->isCorrectAnswer($answers);
        $practice->setCorrectness($this, $correct, $isTraining);

        foreach ($answers as $answerId => $value) {
            $answer = new AnswerUser;
            $answer->answer_id = $answerId;
            $answer->user_id = $this->id;
            $answer->value = $value;
            $answer->save();
        }
    }

    public function isTimedOut() {
        return $this->desired_minutes_to_spend - $this->presence_minutes <= 0;
    }

    public function isSubscriptionActive() {
        return $this->disciplineSubscriptions->map(function ($subscription) {
            return \Carbon\Carbon::parse($subscription->subscription_ends_at) >= \Carbon\Carbon::now();
        })->filter()->isNotEmpty();
    }

    public function getAvailableMoney() {
        return \DB::table('transactions')
            ->selectRaw("SUM(CASE
                WHEN type = 'input' THEN amount
                WHEN type = 'payment' AND from_user_id = {$this->id} THEN -amount
                WHEN type = 'payment' AND from_user_id is null AND to_user_id = {$this->id} THEN amount
                WHEN type = 'withdrawal' THEN -amount
                ELSE 0 END
            ) AS amount")
            ->where('from_user_id', $this->id)
            ->orWhere('to_user_id', $this->id)
            ->first()
            ->amount;
    }

    public function getAvailableRequests() {
        $total = $this->purchases()->sum('requests');
        $used = $this->checkRequests->count();
        return $total - $used;
    }

    public function getAvailableTests() {
        $total = $this->purchases()->sum('tests');
        $used = $this->checkTests->count();
        return $total - $used;
    }

    public function canCreateRequests() {
        return $this->getAvailableRequests() > 0;
    }

    public function canCreateTestRequests() {
        return $this->getAvailableTests() > 0;
    }

    public function addMoney($amount) {
        $transaction = new Transaction;
        $transaction->from_user_id = $this->id;
        $transaction->type = Transaction::TYPE_INPUT;
        $transaction->amount = $amount * 100;
        $transaction->save();

        return $transaction;
    }

    public function purchasePlan(Plan $plan, $disciplineIds = [], $selectedCount = 0) {
        if ($plan->main) {
            $this->purchaseMainPlan($plan, $disciplineIds);
        }
        else {
            $this->purchaseAdditionalPlan($plan, $selectedCount);
        }
    }

    public function purchaseMainPlan(Plan $plan, $disciplineIds = []) {
        if (empty($disciplineIds)) {
            throw new \App\Exceptions\DisciplinesNotSelectedException;
        }

        $money = $this->getAvailableMoney();

        if ($money < $plan->price) {
            throw new \App\Exceptions\NotEnoughMoneyException;
        }

        \DB::beginTransaction();

        foreach ($disciplineIds as $disciplineId) {
            $subscription = $this->disciplineSubscriptions()->whereDisciplineId($disciplineId)->first();

            if ($subscription) {
                $date = $subscription->subscription_ends_at
                    ? \Carbon\Carbon::parse($subscription->subscription_ends_at)
                    : \Carbon\Carbon::now();

                $date = $date->addMonths($plan->months);
                $subscription->subscription_ends_at = $date;
                $subscription->last_renewed_by_main_at = \Carbon\Carbon::now();
                $subscription->save();
            }
            else {
                $this->disciplineSubscriptions()->create([
                    'discipline_id' => $disciplineId,
                    'subscription_ends_at' => \Carbon\Carbon::now()->addMonths($plan->months),
                    'last_renewed_by_main_at' => \Carbon\Carbon::now(),
                ]);
            }
        }

        $transaction = new Transaction;
        $transaction->from_user_id = $this->id;
        $transaction->type = Transaction::TYPE_PAYMENT;
        $transaction->amount = $plan->getPrice(count($disciplineIds));
        $transaction->save();

        $purchase = new Purchase;
        $purchase->user_id = $this->id;
        $purchase->plan_id = $plan->id;
        $purchase->disciplines = $plan->disciplines;
        $purchase->requests = $plan->requests;
        $purchase->months = $plan->months;
        $purchase->tests = $plan->tests;
        $purchase->transaction_id = $transaction->id;
        $purchase->save();

        \DB::commit();
    }

    public function purchaseAdditionalPlan(Plan $plan, $selectedCount = 0) {
        if (!$this->isSubscriptionActive()) {
            throw new \App\Exceptions\SubscriptionNeededException;
        }

        $money = $this->getAvailableMoney();

        // Can only buy one month at a time
        if ($plan->months > 0) {
            $selectedCount = 1;
        }

        if ($selectedCount <= 0) {
            throw new \App\Exceptions\AtLeastOneItemShouldBePurchasedException;
        }

        if ($money < $plan->price * $selectedCount) {
            throw new \App\Exceptions\NotEnoughMoneyException;
        }

        \DB::beginTransaction();

        if ($plan->months > 0) {
            $this->disciplineSubscriptions->each(function ($subscription) use ($selectedCount) {
                $date = $subscription->subscription_ends_at
                    ? \Carbon\Carbon::parse($subscription->subscription_ends_at)
                    : \Carbon\Carbon::now();

                $date = $date->addMonths($selectedCount);
                $subscription->subscription_ends_at = $date;
                $subscription->last_renewed_at = \Carbon\Carbon::now();
                $subscription->save();
            });
        }

        $transaction = new Transaction;
        $transaction->from_user_id = $this->id;
        $transaction->type = Transaction::TYPE_PAYMENT;
        $transaction->amount = $plan->getPrice($selectedCount);
        $transaction->save();

        $requests = $plan->requests > 0 ? $selectedCount : 0;
        $tests = $plan->tests > 0 ? $selectedCount : 0;
        $months = $plan->months > 0 ? $selectedCount : 0;

        $purchase = new Purchase;
        $purchase->user_id = $this->id;
        $purchase->plan_id = $plan->id;
        $purchase->disciplines = $plan->disciplines;
        $purchase->requests = $requests;
        $purchase->months = $months;
        $purchase->tests = $tests;
        $purchase->transaction_id = $transaction->id;
        $purchase->save();

        \DB::commit();
    }

    public function getEarliestMessageTakenAt() {
        return $this->takenMessages()->whereNull('finished_at')->min('taken_at');
    }

    // Get last time subscription was renewed for a discipline by purchasing of main plans
    public function getLastRenewedSubscriptionAt($discipline) {
        return cache()->driver('array')->remember("last_renewed_{$discipline->id}", 1, function() use ($discipline) {
            $subscription = $this->disciplineSubscriptions()
                ->whereDisciplineId($discipline->id)
                ->first();

            return $subscription ? $subscription->last_renewed_by_main_at : null;
        });
    }

    // Remove all practice and theory progress, associated with section
    public function dropSectionProgress($section) {
        // $this->finishedSections()->whereSectionId($section->id)->delete();

        $practiceIds = $section->practicesOfTheoryType->pluck('id');
        $trainingIds = $section->trainings->pluck('id');
        $this->finishedPractices()->whereIn('practice_id', $practiceIds)->delete();
        $this->finishedPractices()->whereIn('practice_id', $trainingIds)->delete();
        $this->practiceAnswers()->whereIn('practice_id', $practiceIds)->delete();
        $this->practiceAnswers()->whereIn('practice_id', $trainingIds)->delete();

        $theoryIds = $section->theoriesWithTheoryPractices->pluck('id');
        $this->finishedTrainingTheories()->whereIn('theory_id', $theoryIds)->delete();

        $answerIds = Answer::whereIn('practice_id', $practiceIds)->pluck('id');
        $this->answers()->whereIn('answer_id', $answerIds)->delete();

        $trainingAnswerIds = Answer::whereIn('practice_id', $trainingIds)->pluck('id');
        $this->answers()->whereIn('answer_id', $trainingAnswerIds)->delete();
    }
}
