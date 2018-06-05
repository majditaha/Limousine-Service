<?php namespace App\Exceptions;

class SubscriptionNeededException extends \Exception {

    public function render($request) {
        return response()->json([
            'error' => 'Необходимо иметь активную подписку, чтобы купить данный тариф',
        ], 400);
    }

};
