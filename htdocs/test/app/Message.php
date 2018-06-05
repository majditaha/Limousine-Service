<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use \Staskjs\LaravelUtils\Traits\CanSyncRelations;

    const TYPE_FAQ = 'faq';
    const TYPE_REVIEW = 'review';
    const TYPE_CHECK_REQUEST = 'check_request';
    const TYPE_CHECK_TEST = 'check_test';
    const TYPE_MESSAGE = 'message';
    const TYPE_MISTAKE = 'mistake';
    const TYPE_RATING_EXPLANATION = 'rating_explanation';

    protected $fillable = [
        'id', 'content', 'practice_id', 'theory_id',
        'public', 'on_main_page', 'type', 'rating',
        'rating_message_id',
    ];

    public static function boot() {
        parent::boot();

        self::creating(function($model) {
            if (empty($model->message_answered_id)) {
                do {
                    $model->uid = str_random(6);
                } while(self::whereUid($model->uid)->exists());
            }
        });
    }

    public function sender() {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function receiver() {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function messageAnswered() {
        return $this->belongsTo(Message::class, 'message_answered_id');
    }

    public function answerMessage() {
        return $this->hasOne(Message::class, 'message_answered_id');
    }

    public function ratingMessage() {
        return $this->hasOne(Message::class, 'rating_message_id');
    }

    public function practice() {
        return $this->belongsTo(Practice::class);
    }

    public function transaction() {
        return $this->belongsTo(Transaction::class);
    }

    public function files() {
        return $this->morphMany(File::class, 'owner')->whereIsImage(false);
    }

    public function images() {
        return $this->morphMany(File::class, 'owner')->whereIsImage(true);
    }

    public function scopeAnswered($query) {
        $query->whereHas('answerMessage');
    }

    public function scopeReviews($query) {
        $query->whereType(self::TYPE_REVIEW);
    }

    public static function getTypes() {
        return [
            self::TYPE_FAQ,
            self::TYPE_REVIEW,
            self::TYPE_CHECK_REQUEST,
            self::TYPE_CHECK_TEST,
            self::TYPE_MESSAGE,
            self::TYPE_MISTAKE,
            self::TYPE_RATING_EXPLANATION,
        ];
    }

    // Create an answer for this message
    public function answer($fromUser, $content) {
        $message = new self;

        $message->message_answered_id = $this->id;
        $message->uid = $this->uid;


        $message->from_user_id = $fromUser->id;

        if ($this->from_user_id == $fromUser->id) {
            $message->to_user_id = $this->to_user_id;
        }
        else {
            $message->to_user_id = $this->from_user_id;
        }

        $message->type = $this->type;
        $message->content = $content;

        $message->save();

        return $message;
    }

    public function getHistoryQuery() {
        return self::with('sender')->whereUid($this->uid)->orderBy('created_at', 'ASC')->orderBy('id', 'ASC');
    }

    public function getHistory() {
        return $this->getHistoryQuery()->get();
    }

    public function syncFiles($files) {
        foreach ($files as $i => $file) {
            $files[$i]['is_image'] = false;
        }
        $this->syncRelation('files', $files, ['is_image', 'name', 'url']);
    }

    public function syncImages($images) {
        foreach ($images as $i => $image) {
            $images[$i]['is_image'] = true;
        }
        $this->syncRelation('images', $images, ['is_image', 'name', 'url']);
    }

    public function markRead() {
        $this->read_at = \Carbon\Carbon::now();
        $this->save();
    }

    public function getPrice($user) {
        $plan = cache()->driver('array')->remember('plan', 1, function() {
            return Plan::whereMain(false)->where('requests', '>', 0)->first();
        });

        if ($user->isTeacher()) {
           if ($this->transaction) {
               return $this->transaction->price;
           }
           return $plan->price - $plan->price * \App\Setting::getValue('commission') / 100;
        }
        return $plan->price;
    }

    public function canTakeMessage($userId) {

        return $this->finished_at == null &&
           ($this->teacher_id == null || $this->teacher_id == $userId) &&
           \Carbon\Carbon::parse($this->taken_at)->diffInMinutes() <= \App\Setting::getValue('teacherAnswerTime');
    }

    public function markTaken($userId) {
        $this->taken_at = \Carbon\Carbon::now();
        $this->teacher_id = $userId;
        $this->save();
    }

    public function markAnswered() {

        $consultPrice = \App\Setting::getValue('consultPrice');

        \DB::beginTransaction();

        $teacher_transaction = new Transaction;
        $teacher_transaction->to_user_id = $this->teacher_id;
        $teacher_transaction->type = Transaction::TYPE_PAYMENT;
        $teacher_transaction->amount = $consultPrice - $consultPrice * \App\Setting::getValue('commission') / 100;
        $teacher_transaction->practice_id = $this->practice_id;
        $teacher_transaction->save();

        $this->finished_at = \Carbon\Carbon::now();
        $this->teacher_transaction_id = $teacher_transaction->id;
        $this->save();

        \DB::commit();
    }

    public function getSubject() {
        switch ($this->type) {
            case self::TYPE_CHECK_REQUEST: case self::TYPE_CHECK_TEST:
                if ($this->messageAnswered) {
                    return 'Ответ на запрос консультации';
                }
                else {
                    return 'Запрос консультации';
                }
            default:
                return 'Новое сообщение';
        }
    }

    public function setRating($rating) {
        $this->rating = $rating;
        $this->save();
    }
}
