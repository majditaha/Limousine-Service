<?php namespace App\Exceptions;

class NotEnoughTestRequestsException extends \Exception {

    public function render($request) {
        return response()->json([
            'error' => 'У вас закончилось количество доступных проверок',
        ], 400);
    }

};
