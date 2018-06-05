<?php namespace App\Exceptions;

class NotEnoughMoneyException extends \Exception {

    public function render($request) {
        return response()->json([
            'error' => 'Невозможно выполнить действие. Недостаточное количество средств на счете',
        ], 400);
    }

};
