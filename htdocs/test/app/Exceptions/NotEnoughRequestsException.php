<?php namespace App\Exceptions;

class NotEnoughRequestsException extends \Exception {

    public function render($request) {
        return response()->json([
            'error' => 'У вас закончилось количество доступных запросов на консультацию',
        ], 400);
    }

};
