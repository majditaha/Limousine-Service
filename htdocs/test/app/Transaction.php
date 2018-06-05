<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    const TYPE_INPUT = 'input';
    const TYPE_PAYMENT = 'payment';
    const TYPE_WITHDRAWAL = 'withdrawal';

    public function fromUser() {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser() {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function discipline() {
        return $this->belongsTo(Discipline::class);
    }

    public function section() {
        return $this->belongsTo(Section::class);
    }

    public function practice() {
        return $this->belongsTo(Practice::class);
    }

    public function scopeFromDate($query, $date) {
        if (!empty($date)) {
            $query->where('created_at', '>=', $date);
        }
    }

    public function scopeToDate($query, $date) {
        if (!empty($date)) {
            $query->where('created_at', '<=', $date);
        }
    }

    public static function getTypes() {
        return [
            self::TYPE_INPUT,
            self::TYPE_PAYMENT,
            self::TYPE_WITHDRAWAL,
        ];
    }

    public static function getTotalBalance($dateFrom, $dateTo) {
        return self::fromDate($dateFrom)
            ->toDate($dateTo)
            ->whereIn('type', ['input', 'withdrawal'])
            ->sum(\DB::raw("CASE WHEN type = 'input' THEN amount ELSE -amount END"));
    }
}
