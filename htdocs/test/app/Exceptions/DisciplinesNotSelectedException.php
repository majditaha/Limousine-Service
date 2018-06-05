<?php namespace App\Exceptions;

class DisciplinesNotSelectedException extends \Exception {

    public function render($request) {
        return response()->json([
            'error' => 'Необходимо выбрать хотя бы один предмет',
        ], 400);
    }

};
